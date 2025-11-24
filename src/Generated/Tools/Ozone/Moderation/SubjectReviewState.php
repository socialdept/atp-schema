<?php

namespace SocialDept\Schema\Generated\Tools\Ozone\Moderation;

enum SubjectReviewState: string
{
    case ReviewOpen = '#reviewOpen';
    case ReviewEscalated = '#reviewEscalated';
    case ReviewClosed = '#reviewClosed';
    case ReviewNone = '#reviewNone';
}
