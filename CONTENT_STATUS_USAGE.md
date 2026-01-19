# Content Status System - Complete Implementation

## ✅ Implemented Components

### 1. **HasContentStatus Trait** ([app/Traits/HasContentStatus.php](app/Traits/HasContentStatus.php))

-   States: `draft`, `review`, `published`, `archived`
-   Scopes: `draft()`, `inReview()`, `published()`, `archived()`
-   Workflow methods: `moveToReview()`, `publish()`, `archive()`, `rollback()`
-   Automatic logging, validation, soft deletes
-   Status badges and labels

### 2. **ContentStatusService** ([app/Services/ContentStatusService.php](app/Services/ContentStatusService.php))

-   Strict workflow validation
-   Permission checks (admin-only transitions)
-   Status history tracking
-   Bulk status updates
-   Statistics

### 3. **ContentStatusLog Model** ([app/Models/ContentStatusLog.php](app/Models/ContentStatusLog.php))

-   Tracks all status changes
-   User attribution
-   Timestamp tracking
-   Human-readable labels

### 4. **Livewire Component** ([app/Livewire/Components/ContentStatusToggle.php](app/Livewire/Components/ContentStatusToggle.php))

-   Visual status badges
-   Dropdown transitions menu
-   Confirmation modals
-   Real-time updates

### 5. **Global Helpers** ([app/helpers.php](app/helpers.php))

-   `contentStatus()` - Service access
-   `statusBadge($status)` - Badge HTML

---

## Usage in Models

### Add Trait to Model

```php
<?php

namespace App\Models;

use App\Traits\HasContentStatus;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasContentStatus;

    protected $fillable = [
        'title',
        'content',
        'status' // Must have status column
    ];
}
```

### Add Status Column Migration

```php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->string('status')->default('draft');
    $table->timestamps();
    $table->softDeletes(); // Required for soft delete policy
});
```

---

## Livewire Integration

### Using Status Toggle Component

```blade
{{-- In any Livewire view --}}
<div class="flex items-center justify-between">
    <h1>{{ $article->title }}</h1>

    <livewire:components.content-status-toggle :model="$article" />
</div>
```

### Manual Status Management

```php
<?php

namespace App\Livewire\Admin;

use App\Models\Article;
use Livewire\Component;

class ArticleForm extends Component
{
    public Article $article;
    public $title;
    public $content;

    public function save()
    {
        $validated = $this->validate([
            'title' => 'required|min:3|max:255',
            'content' => 'required|min:20'
        ]);

        // Create as draft by default
        $this->article = Article::create($validated);

        session()->flash('message', 'Article created as draft');
    }

    public function submitForReview()
    {
        try {
            $this->article->moveToReview();
            session()->flash('message', 'Article submitted for review');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function publishArticle()
    {
        try {
            // Admin check
            $isAdmin = auth()->user()->isAdmin();
            $this->article->publish($isAdmin);

            session()->flash('message', 'Article published successfully');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.article-form');
    }
}
```

---

## Status Workflow

```
┌─────────┐
│  DRAFT  │ ──────────────────────┐
└─────────┘                        │
     │                             │
     │ moveToReview()              │ User can edit
     ↓                             │
┌─────────┐                        │
│ REVIEW  │ ←──────────────────────┘
└─────────┘
     │
     │ publish() [ADMIN ONLY]
     ↓
┌───────────┐
│ PUBLISHED │
└───────────┘
     │
     │ archive() [ADMIN ONLY]
     ↓
┌──────────┐
│ ARCHIVED │ ──→ rollback() [ADMIN ONLY] ──→ PUBLISHED
└──────────┘
```

---

## Scopes Usage

### Query Published Content

```php
// Public-facing pages
$articles = Article::published()->latest()->get();
$article = Article::published()->findOrFail($id);
```

### Admin Dashboard

```php
// Draft articles
$drafts = Article::draft()->count();

// In review
$pending = Article::inReview()->with('user')->get();

// Statistics
$stats = [
    'draft' => Article::draft()->count(),
    'review' => Article::inReview()->count(),
    'published' => Article::published()->count(),
    'archived' => Article::archived()->count()
];
```

---

## Status Checks

```php
// In Blade
@if($article->isPublished())
    <span class="text-green-600">✓ Live</span>
@endif

@if($article->isEditable())
    <a href="{{ route('articles.edit', $article) }}">Edit</a>
@endif

@if($article->isLocked())
    <p class="text-red-600">This content is archived and cannot be edited</p>
@endif

// In Controller/Livewire
if ($article->isDraft()) {
    // Allow editing
}

if (!$article->isPublicVisible()) {
    abort(404); // Hide from public
}
```

---

## Status Badges

### Using Helper

```blade
{!! statusBadge($article->status) !!}

{!! statusBadge('published', 'Live') !!}
```

### Using Model Attribute

```blade
{!! $article->status_badge !!}

<span class="{{ $article->status_badge_color }}">
    {{ $article->status_label }}
</span>
```

---

## Status History

```php
// Get status change log
$history = $article->statusLogs()->with('user')->get();

// In Blade
@foreach($article->statusLogs as $log)
    <div>
        <strong>{{ $log->user->name }}</strong>
        changed status from
        <span class="badge">{{ $log->from_status_label }}</span>
        to
        <span class="badge">{{ $log->to_status_label }}</span>
        on {{ $log->changed_at->format('M d, Y') }}
    </div>
@endforeach

// Using service
$history = contentStatus()->getStatusHistory($article);
```

---

## Safety Features

### 1. Prevent Direct Publish

```php
// ❌ This will throw exception
$article = Article::create(['title' => 'Test', 'status' => 'published']);

// ✅ Correct workflow
$article = Article::create(['title' => 'Test']); // draft
$article->moveToReview();
$article->publish(isAdmin: true);
```

### 2. Admin-Only Transitions

```php
// ❌ Non-admin cannot publish
$article->publish(isAdmin: false); // Throws exception

// ✅ Admin can publish
$article->publish(isAdmin: true);
```

### 3. Workflow Validation

```php
// ❌ Cannot archive draft
$article->archive(); // Throws exception

// ✅ Must be published first
$article->moveToReview();
$article->publish(true);
$article->archive(true);
```

### 4. Soft Delete Only

```php
// Deleting moves to trash (soft delete)
$article->delete();

// View trashed
$trashed = Article::onlyTrashed()->get();

// Restore
$article->restore();

// Force delete (discouraged)
$article->forceDelete();
```

---

## Configuration

### Status Definitions

Edit trait if you need custom statuses ([app/Traits/HasContentStatus.php](app/Traits/HasContentStatus.php)):

```php
public static function getStatuses(): array
{
    return ['draft', 'review', 'published', 'archived'];
}
```

### Admin Check

Implement in User model:

```php
public function isAdmin(): bool
{
    return $this->role === 'admin';
    // or
    return $this->hasRole('admin');
}
```

---

## API Examples

### REST API Controller

```php
public function index()
{
    // Public API - only published
    return Article::published()
        ->with('author')
        ->latest('published_at')
        ->paginate(20);
}

public function show($id)
{
    // Public API
    $article = Article::published()->findOrFail($id);
    return response()->json($article);
}
```

### Admin API

```php
public function adminIndex()
{
    $this->authorize('viewAny', Article::class);

    $status = request('status');

    $query = Article::query();

    if ($status) {
        $query->where('status', $status);
    }

    return $query->with('statusLogs')->paginate();
}

public function updateStatus(Request $request, Article $article)
{
    $this->authorize('update', $article);

    try {
        contentStatus()->changeStatus(
            $article,
            $request->status,
            $request->user()
        );

        return response()->json([
            'message' => 'Status updated successfully',
            'article' => $article->fresh()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage()
        ], 422);
    }
}
```

---

## Testing

```php
public function test_article_defaults_to_draft()
{
    $article = Article::factory()->create();
    $this->assertEquals('draft', $article->status);
}

public function test_can_move_draft_to_review()
{
    $article = Article::factory()->create();
    $article->moveToReview();

    $this->assertEquals('review', $article->status);
}

public function test_non_admin_cannot_publish()
{
    $article = Article::factory()->create(['status' => 'review']);

    $this->expectException(\Exception::class);
    $article->publish(isAdmin: false);
}

public function test_admin_can_publish()
{
    $article = Article::factory()->create(['status' => 'review']);
    $article->publish(isAdmin: true);

    $this->assertEquals('published', $article->status);
}
```

---

## Best Practices

1. **Always use scopes** for public queries:

    ```php
    Article::published()->get(); // Not Article::where('status', 'published')
    ```

2. **Check permissions** before status changes
3. **Display status badges** in admin lists
4. **Log all changes** (automatic with trait)
5. **Use soft deletes** instead of hard deletes
6. **Validate transitions** with service
7. **Show confirmation** for destructive actions

---

**Content Status System ready!** All CMS content now has strict workflow management with logging and safety features.
