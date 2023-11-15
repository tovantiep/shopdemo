<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * App\Models\OrderOrderItem
 *
 * @property int $id
 * @property int $order_id
 * @property int $order_item_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|OrderOrderItem newModelQuery()
 * @method static Builder|OrderOrderItem newQuery()
 * @method static Builder|OrderOrderItem query()
 * @method static Builder|OrderOrderItem whereCreatedAt($value)
 * @method static Builder|OrderOrderItem whereId($value)
 * @method static Builder|OrderOrderItem whereOrderId($value)
 * @method static Builder|OrderOrderItem whereOrderItemId($value)
 * @method static Builder|OrderOrderItem whereUpdatedAt($value)
 * @mixin Eloquent
 */
class OrderOrderItem extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'order_order_items';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'order_item_id'
    ];

}
