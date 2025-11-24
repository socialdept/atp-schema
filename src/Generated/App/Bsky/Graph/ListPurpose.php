<?php

namespace SocialDept\Schema\Generated\App\Bsky\Graph;

enum ListPurpose: string
{
    case Modlist = 'app.bsky.graph.defs#modlist';
    case Curatelist = 'app.bsky.graph.defs#curatelist';
    case Referencelist = 'app.bsky.graph.defs#referencelist';
}
