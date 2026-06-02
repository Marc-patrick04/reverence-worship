<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property string|null $status
 * @property int $user_id
 * @property int|null $assigned_to
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User|null $assignedUser
 * @property-read \App\Models\User\User|null $creator
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereUserId($value)
 */
	class ActionPlan extends \Eloquent {}
}

namespace App\Models\Finance{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int $term
 * @property int $year
 * @property numeric|null $amount
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $payment_date
 * @property string|null $payment_method
 * @property string|null $transaction_id
 * @property string|null $notes
 * @property int|null $submitted_by
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User|null $approver
 * @property-read \App\Models\User\User|null $submitter
 * @property-read \App\Models\User\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereApprovedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereApprovedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereSubmittedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contribution whereYear($value)
 */
	class Contribution extends \Eloquent {}
}

namespace App\Models\Finance{
/**
 * @property int $id
 * @property int $year
 * @property numeric|null $term1_amount
 * @property numeric|null $term2_amount
 * @property numeric|null $term3_amount
 * @property numeric|null $term4_amount
 * @property bool|null $is_active
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting whereTerm1Amount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting whereTerm2Amount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting whereTerm3Amount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting whereTerm4Amount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContributionSetting whereYear($value)
 */
	class ContributionSetting extends \Eloquent {}
}

namespace App\Models\Intercession{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property string|null $status
 * @property int $user_id
 * @property int|null $assigned_to
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User|null $assignedUser
 * @property-read \App\Models\User\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Intercession\ActionPlanTask> $tasks
 * @property-read int|null $tasks_count
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlan whereUserId($value)
 */
	class ActionPlan extends \Eloquent {}
}

namespace App\Models\Intercession{
/**
 * @property int $id
 * @property int $action_plan_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property numeric|null $amount
 * @property string|null $target
 * @property string|null $timeline
 * @property string|null $action_details
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Intercession\ActionPlan|null $actionPlan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereActionDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereActionPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereTimeline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionPlanTask whereUpdatedAt($value)
 */
	class ActionPlanTask extends \Eloquent {}
}

namespace App\Models\Intercession{
/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string|null $bible_verse
 * @property \Illuminate\Support\Carbon $date
 * @property int|null $created_by
 * @property bool|null $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $content_rw
 * @property-read \App\Models\User\User|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion whereBibleVerse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion whereContentRw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DailyDevotion whereUpdatedAt($value)
 */
	class DailyDevotion extends \Eloquent {}
}

namespace App\Models\Intercession{
/**
 * @property int $id
 * @property int $user_id
 * @property int $devotion_id
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\Intercession\DailyDevotion|null $devotion
 * @property-read \App\Models\User\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevotionAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevotionAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevotionAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevotionAttempt whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevotionAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevotionAttempt whereDevotionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevotionAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DevotionAttempt whereUserId($value)
 */
	class DevotionAttempt extends \Eloquent {}
}

namespace App\Models\Intercession{
/**
 * @property int $id
 * @property int $form_id
 * @property int $user_id
 * @property array<array-key, mixed>|null $answers
 * @property float|null $score
 * @property \Illuminate\Support\Carbon|null $submitted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Intercession\SpiritualForm $form
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission whereAnswers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission whereFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormSubmission whereUserId($value)
 */
	class FormSubmission extends \Eloquent {}
}

namespace App\Models\Intercession{
/**
 * @property int $id
 * @property string $title
 * @property string|null $content
 * @property string|null $type
 * @property string|null $file_path
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\User\User|null $creator
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualArchive newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualArchive newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualArchive query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualArchive whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualArchive whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualArchive whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualArchive whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualArchive whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualArchive whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualArchive whereType($value)
 */
	class SpiritualArchive extends \Eloquent {}
}

namespace App\Models\Intercession{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property array<array-key, mixed>|null $questions
 * @property array<array-key, mixed>|null $settings
 * @property bool|null $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Intercession\FormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm whereQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpiritualForm whereUpdatedAt($value)
 */
	class SpiritualForm extends \Eloquent {}
}

namespace App\Models\Music{
/**
 * @property int $id
 * @property string $title
 * @property string $image_path
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $event_date
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $category
 * @property string|null $tags
 * @property string|null $alt_text
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereAltText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereEventDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gallery whereUpdatedAt($value)
 */
	class Gallery extends \Eloquent {}
}

namespace App\Models\Music{
/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Music\Song> $songs
 * @property-read int|null $songs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlist whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlist whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlist whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Playlist whereUpdatedAt($value)
 */
	class Playlist extends \Eloquent {}
}

namespace App\Models\Music{
/**
 * @property int $id
 * @property int|null $playlist_id
 * @property int|null $song_id
 * @property int|null $display_order
 * @property string|null $created_at
 * @property-read \App\Models\Music\Playlist|null $playlist
 * @property-read \App\Models\Music\Song|null $song
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistSong newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistSong newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistSong query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistSong whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistSong whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistSong whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistSong wherePlaylistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistSong whereSongId($value)
 */
	class PlaylistSong extends \Eloquent {}
}

namespace App\Models\Music{
/**
 * @property int $id
 * @property string $service_name
 * @property int|null $number_of_teams
 * @property \Illuminate\Support\Carbon|null $generated_at
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Music\TeamMember> $members
 * @property-read int|null $members_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceTeam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceTeam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceTeam whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceTeam whereGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceTeam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceTeam whereNumberOfTeams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceTeam whereServiceName($value)
 */
	class ServiceTeam extends \Eloquent {}
}

namespace App\Models\Music{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string|null $email
 * @property string|null $voice_part
 * @property string|null $performance_level
 * @property string|null $phone
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer wherePerformanceLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Singer whereVoicePart($value)
 */
	class Singer extends \Eloquent {}
}

namespace App\Models\Music{
/**
 * @property int $id
 * @property string $title
 * @property string|null $artist
 * @property string|null $key_signature
 * @property int|null $tempo
 * @property string|null $lyrics
 * @property string|null $youtube_link
 * @property string|null $assigned_singer
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Music\Playlist> $playlists
 * @property-read int|null $playlists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereArtist($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereAssignedSinger($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereKeySignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereLyrics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereTempo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereYoutubeLink($value)
 */
	class Song extends \Eloquent {}
}

namespace App\Models\Music{
/**
 * @property int $id
 * @property int|null $service_team_id
 * @property int $team_number
 * @property int|null $user_id
 * @property string|null $voice_part
 * @property string|null $performance_level
 * @property string|null $created_at
 * @property-read \App\Models\Music\ServiceTeam|null $serviceTeam
 * @property-read \App\Models\User\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember wherePerformanceLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereServiceTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereTeamNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereVoicePart($value)
 */
	class TeamMember extends \Eloquent {}
}

namespace App\Models\Music{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int|null $leader_id
 * @property int|null $member_count
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\User|null $creator
 * @property-read \App\Models\User\User|null $leader
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\User> $members
 * @property-read int|null $members_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup whereLeaderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup whereMemberCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorshipGroup whereUpdatedAt($value)
 */
	class WorshipGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property bool|null $is_pinned
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicBoard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicBoard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicBoard query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicBoard whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicBoard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicBoard whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicBoard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicBoard whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicBoard whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicBoard whereUpdatedAt($value)
 */
	class PublicBoard extends \Eloquent {}
}

namespace App\Models\System{
/**
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string $description
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\User\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActivityLog whereUserId($value)
 */
	class ActivityLog extends \Eloquent {}
}

namespace App\Models\System{
/**
 * @property int $id
 * @property string $error_type
 * @property string $message
 * @property string|null $file_path
 * @property int|null $line_number
 * @property string|null $stack_trace
 * @property int|null $user_id
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\User\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog whereErrorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog whereLineNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog whereStackTrace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ErrorLog whereUserId($value)
 */
	class ErrorLog extends \Eloquent {}
}

namespace App\Models\System{
/**
 * @property int $id
 * @property int $page_id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\System\Page $page
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature wherePageId($value)
 */
	class Feature extends \Eloquent {}
}

namespace App\Models\System{
/**
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string|null $icon
 * @property string|null $route
 * @property int|null $sort_order
 * @property bool|null $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\System\Feature> $features
 * @property-read int|null $features_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 */
	class Page extends \Eloquent {}
}

namespace App\Models\System{
/**
 * @property int $id
 * @property string $setting_key
 * @property string|null $setting_value
 * @property string|null $setting_type
 * @property string|null $description
 * @property int|null $updated_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereSettingKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereSettingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereSettingValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemSetting whereUpdatedBy($value)
 */
	class SystemSetting extends \Eloquent {}
}

namespace App\Models\User{
/**
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models\User{
/**
 * @property int $id
 * @property int $role_id
 * @property int $page_id
 * @property int $feature_id
 * @property string|null $created_at
 * @property-read \App\Models\User\Role $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePageFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePageFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePageFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePageFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePageFeature whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePageFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePageFeature wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RolePageFeature whereRoleId($value)
 */
	class RolePageFeature extends \Eloquent {}
}

namespace App\Models\User{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property bool|null $is_active
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property string|null $province
 * @property string|null $district
 * @property string|null $sector
 * @property string|null $village
 * @property string|null $gender
 * @property string|null $marital_status
 * @property string|null $membership_type
 * @property string|null $occupation
 * @property string|null $ministry_role
 * @property string|null $emergency_contact
 * @property string|null $emergency_name
 * @property string|null $skills
 * @property string|null $notes
 * @property bool|null $is_singer
 * @property string|null $voice_part
 * @property string|null $singer_level
 * @property string|null $singer_notes
 * @property string|null $google_id
 * @property string|null $avatar
 * @property string|null $profile_photo
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmergencyContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmergencyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGoogleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsSinger($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMaritalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMembershipType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMinistryRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOccupation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSector($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSingerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSingerNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSkills($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVillage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVoicePart($value)
 */
	class User extends \Eloquent {}
}

