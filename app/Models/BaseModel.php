<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations;

class BaseModel extends Model {

    /**
     * whereHas 的 where in 实现
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $relationName
     * @param callable $callable
     * @return Builder
     *
     * @throws Exception
     */
    public function scopeWhereHasIn($builder, $relationName, callable $callable) {
        $relationNames = explode('.', $relationName);
        $nextRelation = implode('.', array_slice($relationNames, 1));

        $method = $relationNames[0];

        /** @var Relations\BelongsTo|Relations\HasOne $relation */
        $relation = Relations\Relation::noConstraints(function () use ($method) {
            return $this->$method();
        });

        /** @var Builder $in */
        if($nextRelation){
            $in = $relation->getQuery()->whereHasIn($nextRelation, $callable);
        } else {
            $in = $relation->getQuery()->where($callable);
        }

        if ($relation instanceof Relations\BelongsTo) {
            return $builder->whereIn($relation->getForeignKey(), $in->select($relation->getOwnerKey()));
        } elseif ($relation instanceof Relations\HasOne) {
            return $builder->whereIn($this->getKeyName(), $in->select($relation->getForeignKeyName()));
        } elseif ($relation instanceof Relations\HasMany){
            return $builder->whereIn($this->getKeyName(), $in->select($relation->getForeignKeyName()));
        }

        throw new \Exception(__METHOD__ . " 不支持 " . get_class($relation));
    }

    /**
     * 构造查询条件
     *
     * @var array
     */
    public function scopeTables($self,$query=null,$select=null) {

        extract(Request::only('where', 'whereIn', 'orderBy', 'pageSize' ,'page', 'time'));

        $data = $self;

        if (!empty($select)) {
            $data->{"select"}($select);
        }

        // query条件查询
        if (!empty($query)) {
            foreach ($query as $key => $value) {
                if (!empty($value)) {
                    $data->{"where"}($value['key'],$value['sign'],$value['value']);
                }
            }
        }

        // where条件查询
        if (!empty($where)) {
            $data->where(function($q) use($where) {
                foreach ($where as $key => $value) {
                    if (!empty($value)) {
                        if(!empty($value['isor']) && $value['isor'] == 1){
                            $q->{"orWhere"}($value['key'],$value['sign'],$value['value']);
                        }else{
                            $q->{"where"}($value['key'],$value['sign'],$value['value']);
                        }
                    }
                }
            });

        }

        // whereIn条件查询
        if (!empty($whereIn)) {
            foreach ($whereIn as $key => $value) {
                if (!empty($value['value'])) {
                    $data->{"whereIn"}($value['key'],$value['value']);
                }
            }
        }

        if (!empty($time)) {
            foreach ($time as $key => $value) {
                if (!empty($value)) {
                    $data->{"where"}($value['key'],$value['sign'],$value['value']);
                }
            }
        }


        // 排序
        if (!empty($orderBy)) {
            foreach ($orderBy as $key => $value) {
                if (!empty($value)) {
                    $data->{"orderBy"}($value['key'],$value['value']);
                }
            }
        }

        $temp =  $data;
        $count = $temp->count();
        unset($temp);

        // 分页
        $pageSize = !empty($pageSize) ? $pageSize : 10;
        $page = !empty($page) ? $page : 1;
        $skip = ($page - 1) * $pageSize;
        $take = $pageSize;
        $data->{"skip"}($skip)->{"take"}($take);

        $list = $data->get()->toArray();
        return ['list' => $list, 'count'=>$count];
    }
}

