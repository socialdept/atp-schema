<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Sync;

enum HostStatus: string
{
    case Active = 'active';
    case Idle = 'idle';
    case Offline = 'offline';
    case Throttled = 'throttled';
    case Banned = 'banned';
}
