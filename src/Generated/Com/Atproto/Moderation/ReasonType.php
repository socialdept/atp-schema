<?php

namespace SocialDept\Schema\Generated\Com\Atproto\Moderation;

/**
 * GENERATED CODE - DO NOT EDIT
 */
enum ReasonType: string
{
    case ReasonSpam = 'com.atproto.moderation.defs#reasonSpam';
    case ReasonViolation = 'com.atproto.moderation.defs#reasonViolation';
    case ReasonMisleading = 'com.atproto.moderation.defs#reasonMisleading';
    case ReasonSexual = 'com.atproto.moderation.defs#reasonSexual';
    case ReasonRude = 'com.atproto.moderation.defs#reasonRude';
    case ReasonOther = 'com.atproto.moderation.defs#reasonOther';
    case ReasonAppeal = 'com.atproto.moderation.defs#reasonAppeal';
    case ReportReasonAppeal = 'tools.ozone.report.defs#reasonAppeal';
    case ReportReasonOther = 'tools.ozone.report.defs#reasonOther';
    case ReasonViolenceAnimal = 'tools.ozone.report.defs#reasonViolenceAnimal';
    case ReasonViolenceThreats = 'tools.ozone.report.defs#reasonViolenceThreats';
    case ReasonViolenceGraphicContent = 'tools.ozone.report.defs#reasonViolenceGraphicContent';
    case ReasonViolenceGlorification = 'tools.ozone.report.defs#reasonViolenceGlorification';
    case ReasonViolenceExtremistContent = 'tools.ozone.report.defs#reasonViolenceExtremistContent';
    case ReasonViolenceTrafficking = 'tools.ozone.report.defs#reasonViolenceTrafficking';
    case ReasonViolenceOther = 'tools.ozone.report.defs#reasonViolenceOther';
    case ReasonSexualAbuseContent = 'tools.ozone.report.defs#reasonSexualAbuseContent';
    case ReasonSexualNCII = 'tools.ozone.report.defs#reasonSexualNCII';
    case ReasonSexualDeepfake = 'tools.ozone.report.defs#reasonSexualDeepfake';
    case ReasonSexualAnimal = 'tools.ozone.report.defs#reasonSexualAnimal';
    case ReasonSexualUnlabeled = 'tools.ozone.report.defs#reasonSexualUnlabeled';
    case ReasonSexualOther = 'tools.ozone.report.defs#reasonSexualOther';
    case ReasonChildSafetyCSAM = 'tools.ozone.report.defs#reasonChildSafetyCSAM';
    case ReasonChildSafetyGroom = 'tools.ozone.report.defs#reasonChildSafetyGroom';
    case ReasonChildSafetyPrivacy = 'tools.ozone.report.defs#reasonChildSafetyPrivacy';
    case ReasonChildSafetyHarassment = 'tools.ozone.report.defs#reasonChildSafetyHarassment';
    case ReasonChildSafetyOther = 'tools.ozone.report.defs#reasonChildSafetyOther';
    case ReasonHarassmentTroll = 'tools.ozone.report.defs#reasonHarassmentTroll';
    case ReasonHarassmentTargeted = 'tools.ozone.report.defs#reasonHarassmentTargeted';
    case ReasonHarassmentHateSpeech = 'tools.ozone.report.defs#reasonHarassmentHateSpeech';
    case ReasonHarassmentDoxxing = 'tools.ozone.report.defs#reasonHarassmentDoxxing';
    case ReasonHarassmentOther = 'tools.ozone.report.defs#reasonHarassmentOther';
    case ReasonMisleadingBot = 'tools.ozone.report.defs#reasonMisleadingBot';
    case ReasonMisleadingImpersonation = 'tools.ozone.report.defs#reasonMisleadingImpersonation';
    case ReasonMisleadingSpam = 'tools.ozone.report.defs#reasonMisleadingSpam';
    case ReasonMisleadingScam = 'tools.ozone.report.defs#reasonMisleadingScam';
    case ReasonMisleadingElections = 'tools.ozone.report.defs#reasonMisleadingElections';
    case ReasonMisleadingOther = 'tools.ozone.report.defs#reasonMisleadingOther';
    case ReasonRuleSiteSecurity = 'tools.ozone.report.defs#reasonRuleSiteSecurity';
    case ReasonRuleProhibitedSales = 'tools.ozone.report.defs#reasonRuleProhibitedSales';
    case ReasonRuleBanEvasion = 'tools.ozone.report.defs#reasonRuleBanEvasion';
    case ReasonRuleOther = 'tools.ozone.report.defs#reasonRuleOther';
    case ReasonSelfHarmContent = 'tools.ozone.report.defs#reasonSelfHarmContent';
    case ReasonSelfHarmED = 'tools.ozone.report.defs#reasonSelfHarmED';
    case ReasonSelfHarmStunts = 'tools.ozone.report.defs#reasonSelfHarmStunts';
    case ReasonSelfHarmSubstances = 'tools.ozone.report.defs#reasonSelfHarmSubstances';
    case ReasonSelfHarmOther = 'tools.ozone.report.defs#reasonSelfHarmOther';
}
