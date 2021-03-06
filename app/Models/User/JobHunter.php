<?php

namespace App\Models\User;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobHunter extends BaseModel
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
    protected $table = 'jobhunter';
    
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
    protected $fillable = ['user_id', 'name', 'job_experience', 'job_education', 'status', 'phone', 'email', 'wx_code', 'sex', 'birthday', 'advantage', 'job_status', 'pdf', 'pdf_name', 'pdf_update'];

    /**
     * 模型日期列的存储格式。
     *
     * @var string
     */
    protected $dateFormat = 'U';
    
    public function experience()
    {
        return $this->hasMany('App\Models\User\Experience', 'jobhunter_id', 'id');
    }

    public function position()
    {
        return $this->hasMany('App\Models\User\JobHunterPosition', 'jobhunter_id', 'id');
    }

    public function getExpectPositionAttribute($value)
    {
        return json_decode($value, true);
    }
}
