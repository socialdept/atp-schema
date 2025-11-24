<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Moderation;

/**
 * Tag describing a type of subject that might be reported.
 */
enum SubjectType: string
{
    case Account = 'account';
    case Record = 'record';
    case Chat = 'chat';
}
