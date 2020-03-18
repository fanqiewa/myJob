<?php

namespace App\Models\User;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobHunterPosition extends BaseModel
{
    /**
     * 软删除，数据库中必须要有deleted_at字段
     */
    use SoftDeletes;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'jobhunter_position';
    
    /**
     * 指定是否模型应该被戳记时间。
     *
     * @var bool
     */
    public $timestamps = true;
    
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['jobhunter_id', 'position_id', 'salary_id', 'city_id'];

    /**
     * 模型日期列的存储格式。
     *
     * @var string
     */
    protected $dateFormat = 'U';
    
    public function getPositionIdAttribute($value)
    {
        return json_decode($value, true);
    }
    public function getCityIdAttribute($value)
    {
        return json_decode($value, true);
    }
}
