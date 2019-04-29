<?php

namespace App\Shop\Models;

use App\Shop\Enums\EProductStatuses;
use DomainException;
use Kyslik\ColumnSortable\Sortable;

/**
 * Product model
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $description
 * @property int $status
 * @property int $main_category_id
 * @property int $brand_id
 * @property float $old_price
 * @property float $price
 * @property float $rating
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property integer $created_at
 * @property integer $updated_at
 * @property Category $mainCategory
 * @property Brand $brand
 */
class Product extends ShopModel
{

    use Sortable;

    /**
     * {@inheritdoc}
     */
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'main_category_id',
        'brand_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];


    /**
     * {@inheritdoc}
     */
    protected $table = 'shop_products';



    /**
     * Get main category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainCategory()
    {
        return $this->belongsTo(Category::class, 'main_category_id');
    }


    /**
     * Get brand.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }


    /**
     * {@inheritdoc}
     */
    public function canDelete(): bool
    {
        // TODO
        return true;
    }


    /**
     * Return is the this product active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === EProductStatuses::ACTIVE;
    }


    /**
     * Sets this product price.
     *
     * @param mixed $price
     * @param mixed $oldPrice
     */
    public function setPrice($price, $oldPrice = null): void
    {
        if ($price <= 0) {
           throw new DomainException('Incorrect price');
        }
        $this->price = round($price, 2);
        if ($oldPrice > 0) {
            $this->old_price = round($oldPrice, 2);
        }
        $this->saveOrFail();
    }


    /**
     * Sets this product new status.
     *
     * @param int $newStatus
     */
    public function setNewStatus(int $newStatus): void
    {
        if ($this->status == $newStatus) {
            return;
        }
        $availableStatuses = array_keys(EProductStatuses::getLabels());
        if ( !in_array($newStatus, $availableStatuses)) {
           throw new DomainException('Incorrect status');
        }
        // Status Active can only be available for product with price
        if (($newStatus == EProductStatuses::ACTIVE) && ($this->price <= 0)) {
            throw new DomainException('Status Active can only be available for product with price');
        }

        $this->status = $newStatus;
        $this->saveOrFail();
    }

}
