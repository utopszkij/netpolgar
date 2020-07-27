<?php

class ProjectRecord {
    public $id = 0;          // record ID
    public $name = '';
    public $state = 'proposal';  // 'proposal', 'active', 'ended', 'closed', 'draft', 'waiting'
    public $description = '';
    public $avatar = ''; 
    public $deadline = '';
    public $project_to_active = 5;
    public $project_to_close = 90;
    public $member_to_active = 2;
    public $member_to_exclude = 90;
}

class ProjectsModel extends Model {
    function __construct() {
        $this->tableName = 'projects';
        $db = new DB();
        $db->createTable('projects',
            [['id','INT',11,true],
             ['name','VARCHAR',128,false],
             ['state','VARCHAR',32,false],
             ['description','TEXT',0,false],
             ['avatar','VARCHAR',80,false],
             ['deadline','VARCHAR',10,false],
             ['project_to_active','INT',3,false],
             ['project_to_close','INT',3,false],
             ['member_to_active','INT',3,false],
             ['member_to_exclude','INT',3,false]
            ],
            ['id','name','deadline']
            );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create projects table '.$db->getErrorMsg(); exit();
        }
    }

    /**
     * get rekord set
     * @param object $p -  offset, limit, orderField, orderDir, searchStr, filterState, opcionálisan member_id
     * @param int $total
     * @return array
     */
    public function getProjectRecords($p, int &$total): array {
        $total = 0;
        if ($p->offset == '') {
            $p->offset = 0;
        }
        if ($p->limit == '') {
            $p->limit = 20;
        }
        $filter = new Filter('projects','p');
        $filter->setColumns('p.id, p.avatar, p.name,  p.state, p.deadline');
        if ($p->member_id != '') {
            $filter->join('LEFT OUTER JOIN','members','m',
                'type = "projects" and object_id=p.id and user_id = "'.$filter->quote($p->member_id).'"');            
        }
        if ($p->searchstr != '') {
            $filter->where(['p.name','like','%'.$p->searchstr.'%']);
        }
        if ($p->filterState != '') {
            $filter->where(['p.state','=',$p->filterState]);
        }
        if ($p->member_id != '') {
            $filter->where(['m.user_id','=',$p->member_id]);
        }
        $filter->order($p->order.' '.$p->order_dir);
        $filter->offset($p->offset);
        $filter->limit($p->limit);
        $total = $filter->count();
        return $filter->get();
    }
    
    /**
     * project statusz automatikus modositása a like számok alapján
     * @param int $id
     */
    public function autoUpdate(int $id) {
        $project = $this->getRecord($id);
        if (!$project) {
            echo 'Fatal error project '.$id.' not fiund'; exit();
        }
        if (($project->id > 0) & (($project->state == 'proposal') | ($project->state == 'active'))) {
            $table = new Table('memebrs');
            $table->where(['type','==','projects']);
            $table->where(['object_id','==',$id]);
            $table->where(['state','==','active']);
            $memberCount = $table->count();
            $table = new Table('memebrs');
            $table->where(['type','==','projects']);
            $table->where(['object_id','==',$id]);
            $table->where(['state','==','admin']);
            $memberCount = $memberCount + $table->count();
            
            $table = new Table('likes');
            $table->where(['type','==','projects']);
            $table->where(['object_id','==',$id]);
            $table->where(['like_type','==','like']);
            $likeCount = $table->count();
            
            $table = new Table('likes');
            $table->where(['type','==','projects']);
            $table->where(['object_id','==',$id]);
            $table->where(['like_type','==','dislike']);
            $dislikeCount = $table->count();
            
            if (($project->state == 'proposal') &
                ((($likeCount - $dislikeCount) >= $project->project_to_active) |
                    ($likeCount >= $memberCount)
                    )
                ) {
                    $project->state = 'active';
                    $table = new table('projects');
                    $table->update($project);
            }
            if (($project->state == 'active') &
                    ((($dislikeCount - $likeCount) >= ($memberCount * $project->project_to_close / 100)) |
                        ($dislikeCount >= $memberCount)
                        )
                    ) {
                        $project->state = 'closed';
                        $table = new table('projects');
                        $table->update($project);
            }
        }
    }
    
    /**
     * utolsó néhány projekt lekérdezése
     * @param $limit 
     * @return array of ProjectRecords
     */
    public function newProjects(int $limit = 3) {
        $table = new Table('projects');
        $table->order('id DESC');
        $table->limit($limit);
        return $table->get();
    }
} // class
?>
