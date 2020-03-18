<?php

namespace App\Models\Area;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends BaseModel
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
    protected $table = 'area';
    
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
    protected $fillable = ['name', 'last_id'];

    /**
     * 模型日期列的存储格式。
     *
     * @var string
     */
    protected $dateFormat = 'U';

    
    public function children()
    {
        return $this->hasMany('App\Models\Area\Area', 'last_id', 'id');
    }
}
