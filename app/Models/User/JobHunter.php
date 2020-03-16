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
    protected $fillable = ['user_id', 'name', 'experience', 'education', 'status', 'phone', 'email', 'wx_code', 'sex', 'birthday', 'advantage', 'expect_position', 'work_experience', 'education_experience', 'job_status', 'pdf', 'pdf_name', 'pdf_update'];

    /**
     * 模型日期列的存储格式。
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
