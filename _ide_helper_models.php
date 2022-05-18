<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\ChatConversation
 *
 * @property int $id
 * @property int $coach_user_id
 * @property int $client_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $clientUser
 * @property-read \App\Models\User $coachUser
 * @property-read \App\Models\ChatMessage|null $lastMessage
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatMessage[] $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatParticipant[] $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation whereClientUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation whereCoachUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatConversation whereUpdatedAt($value)
 */
	class ChatConversation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ChatMessage
 *
 * @property int $id
 * @property mixed $message
 * @property int $seen
 * @property int $user_id
 * @property int $chat_conversation_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ChatConversation $conversation
 * @property-read \App\Models\ChatParticipant $participant
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereChatConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatMessage whereUserId($value)
 */
	class ChatMessage extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ChatParticipant
 *
 * @property int $id
 * @property int $user_id
 * @property int $chat_conversation_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ChatConversation $conversation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ChatMessage[] $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereChatConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatParticipant whereUserId($value)
 */
	class ChatParticipant extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Token
 *
 * @property int $id
 * @property string $token
 * @property string|null $push_token
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token query()
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token wherePushToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUserId($value)
 */
	class Token extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $rebase_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRebaseUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

