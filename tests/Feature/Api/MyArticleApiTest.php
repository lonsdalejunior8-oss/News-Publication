<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use App\Models\User;
use App\Notifications\ArticleSubmittedForApproval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class MyArticleApiTest extends TestCase
{
    use RefreshDatabase;

    protected function makeArticle(User $author, array $overrides = []): Article
    {
        $title = $overrides['title'] ?? 'Test Article '.Str::random(6);

        $article = new Article(array_merge([
            'title' => $title,
            'slug' => Article::generateUniqueSlug($title),
            'body' => 'Body text',
            'status' => 'draft',
        ], $overrides));
        $article->author_id = $author->id;
        $article->save();

        return $article;
    }

    public function test_author_only_sees_their_own_articles(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $other = User::factory()->create(['role' => 'author']);

        $this->makeArticle($author, ['title' => 'Mine']);
        $this->makeArticle($other, ['title' => 'Not mine']);

        $response = $this->actingAs($author, 'sanctum')->getJson('/api/my/articles');

        $response->assertOk();
        $titles = collect($response->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('Mine'));
        $this->assertFalse($titles->contains('Not mine'));
    }

    public function test_author_can_create_a_draft(): void
    {
        $author = User::factory()->create(['role' => 'author']);

        $response = $this->actingAs($author, 'sanctum')->postJson('/api/my/articles', [
            'title' => 'New Draft',
            'excerpt' => 'excerpt',
            'body' => 'body content',
        ]);

        $response->assertCreated()->assertJsonPath('data.status', 'draft');
        $this->assertDatabaseHas('articles', ['title' => 'New Draft', 'author_id' => $author->id, 'status' => 'draft']);
    }

    public function test_author_can_upload_a_featured_image_and_gallery_images_when_creating(): void
    {
        Storage::fake('public');
        $author = User::factory()->create(['role' => 'author']);

        $response = $this->actingAs($author, 'sanctum')->post('/api/my/articles', [
            'title' => 'With Photos',
            'body' => 'body content',
            'featured_image' => UploadedFile::fake()->image('featured.jpg'),
            'images' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.jpg')],
        ]);

        $response->assertCreated();
        $this->assertNotNull($response->json('data.featured_image_url'));
        $this->assertCount(2, $response->json('data.images'));

        $article = Article::where('title', 'With Photos')->firstOrFail();
        Storage::disk('public')->assertExists($article->featured_image_path);
        $this->assertCount(2, $article->images);
    }

    public function test_updating_with_a_new_featured_image_deletes_the_old_file(): void
    {
        // NOTE: this exercises Laravel's test client, which builds the Request in-process and does
        // NOT go through real PHP multipart parsing — real PUT requests with a multipart body don't
        // populate $_FILES (or any field) at all in production; the mobile client must POST with a
        // `_method=PUT` spoof field instead. Verified manually against the live app; not something
        // this in-process test client can catch, so it's called out here for the next person.
        Storage::fake('public');
        $author = User::factory()->create(['role' => 'author']);
        $article = $this->makeArticle($author);
        $article->featured_image_path = UploadedFile::fake()->image('old.jpg')->store('articles', 'public');
        $article->save();
        $oldPath = $article->featured_image_path;

        $this->actingAs($author, 'sanctum')->put("/api/my/articles/{$article->id}", [
            'title' => $article->title,
            'body' => $article->body,
            'featured_image' => UploadedFile::fake()->image('new.jpg'),
        ])->assertOk();

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($article->fresh()->featured_image_path);
    }

    public function test_author_cannot_update_another_authors_article(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $other = User::factory()->create(['role' => 'author']);
        $article = $this->makeArticle($other);

        $this->actingAs($author, 'sanctum')
            ->putJson("/api/my/articles/{$article->id}", ['title' => 'Hijacked', 'body' => 'x'])
            ->assertForbidden();
    }

    public function test_submit_notifies_admins_and_moves_to_pending(): void
    {
        Notification::fake();

        $author = User::factory()->create(['role' => 'author']);
        $admin = User::factory()->create(['role' => 'admin']);
        $article = $this->makeArticle($author, ['status' => 'draft']);

        $this->actingAs($author, 'sanctum')
            ->postJson("/api/my/articles/{$article->id}/submit")
            ->assertOk()
            ->assertJsonPath('data.status', 'pending');

        Notification::assertSentTo($admin, ArticleSubmittedForApproval::class);
    }

    public function test_submit_only_allowed_from_draft_or_rejected(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $article = $this->makeArticle($author, ['status' => 'published']);

        $this->actingAs($author, 'sanctum')
            ->postJson("/api/my/articles/{$article->id}/submit")
            ->assertForbidden();
    }
}
