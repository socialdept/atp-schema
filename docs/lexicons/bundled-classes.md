# Bundled classes

Part of the [atp-schema docs](../introduction.md#documentation). The PHP classes and enums shipped for the standard AT Protocol and Bluesky lexicons.

The package ships classes for the `app.bsky`, `chat.bsky`, `com.atproto` and `tools.ozone` lexicons under `SocialDept\AtpSchema\Generated`, more than 220 in all. They come from the same generator as [Generating classes](generating.md), so you can use them without a generation step.

```php
use SocialDept\AtpSchema\Generated\App\Bsky\Feed\Post;
use SocialDept\AtpSchema\Generated\Com\Atproto\Repo\StrongRef;

$post = new Post(
    text: 'Hello ATP!',
    createdAt: now(),
);

$ref = new StrongRef(
    uri: 'at://did:plc:example/app.bsky.feed.post/123',
    cid: 'bafyreic3...',
);
```

## Working with a class

Every class extends `SocialDept\AtpSchema\Data\Data`:

```php
$post = Post::fromArray($array);       // also fromJson($json)
$post = Post::fromRecord($record);     // reads $record['value'] when it is there

$post->toArray();                      // optional fields that are null are left out
$post->toRecord();                     // toArray() plus '$type' => 'app.bsky.feed.post'
$post->toJson();

Post::getLexicon();                    // 'app.bsky.feed.post'
$post->with(['text' => 'Edited']);     // a copy with some properties changed
$post->equals($other);
```

`$post->validate()` checks the instance against its lexicon through `schema_validate()`. An exception during validation counts as valid, so for the reason a record fails, validate its array with the facade (see [Validating records](../validation/validating.md)):

```php
use SocialDept\AtpSchema\Facades\Schema;

Schema::validateWithErrors('app.bsky.feed.post', $post->toArray());
```

## What is included

The namespaces follow the NSIDs. A few of the classes:

- **Records:** `App\Bsky\Feed\Post`, `Feed\Like`, `Feed\Repost`, `Graph\Follow`, `Graph\Block`, `Graph\ListRecord` (for `app.bsky.graph.list`, since `List` is a reserved word), `Graph\Listitem`, `Labeler\Service`.
- **Embeds:** `App\Bsky\Embed\Images`, `Embed\External`, `Embed\Record`, `Embed\RecordWithMedia`, `Embed\Video`.
- **Feed views:** `App\Bsky\Feed\Defs\PostView`, `FeedViewPost`, `ThreadViewPost`, `ReplyRef`, `ViewerState`.
- **Actor views:** `App\Bsky\Actor\Defs\ProfileView`, `ProfileViewBasic`, `ProfileViewDetailed`, `ViewerState`, and the preference classes.
- **Graph views:** `App\Bsky\Graph\Defs\ListView`, `ListItemView`, `Relationship`, `StarterPackView`.
- **Rich text:** `App\Bsky\Richtext\Facet`, with `Facet\Mention`, `Facet\Link`, `Facet\Tag` and `Facet\ByteSlice`.
- **AT Protocol core:** `Com\Atproto\Repo\StrongRef`, `Repo\Defs\CommitMeta`, `Label\Defs\Label`, `Admin\Defs\*`, `Server\Defs\*`, and the six `Sync\SubscribeRepos\*` events.
- **Chat:** `Chat\Bsky\Convo\Defs\*`, the conversation, message and log views.
- **Moderation:** `Tools\Ozone\Moderation\Defs\*` and `Tools\Ozone\Communication\Defs\TemplateView`.

## Enums

String types with a fixed list of known values become backed enums:

| Enum | Cases |
|---|---|
| `Com\Atproto\Moderation\ReasonType` | Report reasons, such as `ReasonSpam` and `ReasonViolation` |
| `Com\Atproto\Moderation\SubjectType` | `Account`, `Record`, `Chat` |
| `Com\Atproto\Label\LabelValue` | Content label values |
| `Com\Atproto\Sync\HostStatus` | `Active`, `Idle`, `Offline`, `Throttled`, `Banned` |
| `App\Bsky\Graph\ListPurpose` | `Modlist`, `Curatelist`, `Referencelist` |
| `App\Bsky\Actor\MutedWordTarget` | `Content`, `Tag` |
| `Tools\Ozone\Moderation\SubjectReviewState` | `ReviewOpen`, `ReviewEscalated`, `ReviewClosed`, `ReviewNone` |

## The source lexicons

The JSON these classes were made from ships in the package's `resources/lexicons`. Copy it into your app with `php artisan vendor:publish --tag=atp-lexicons`.
