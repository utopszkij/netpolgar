<?php

class TaskRecord {
    public $id = 0;          // record ID
    public $description = '';
    public $project_id = 0;
    public $state = ''; // wait_req, wait_run, runing, wait_control, closed
    public $sequence = 0; // ordering in state
    public $deadline = '';
    public $tasktype = 'task'; // task, bug, request, suggestion, comment
    public $reqclosed = '';
    public $reqnotrun = '';
    public $nick = ''; // hozzárendelt user nickname
}

class TasksModel extends Model {
    function __construct() {
        $this->tableName = 'tasks';
        $db = new DB();
        $db->createTable('tasks',
            [['id','INT',11,true],
             ['description','TEXT',0,false],
             ['project_id','INT',11,false],
             ['state','VARCHAR',32,false],
             ['sequence','INT',11,false],
             ['deadline','VARCHAR',10,false],
             ['tasktype','VARCHAR',10,false],
             ['reqclosed','VARCHAR',10,false],
             ['reqnotrun','VARCHAR',10,false],
             ['nick','VARCHAR',32,false]
            ],
            ['id','project_id','deadline']
            );
        if ($db->getErrorMsg() != '') {
            echo 'Fatal error in create tasks table '.$db->getErrorMsg(); exit();
        }
    }

    /**
     * get rekord set
     * @param object $p -  project_id, offset, limit, orderField, orderDir, searchStr, filterState, opcionálisan member_id
     * @param int $total
     * @return array
     */
    public function getTasksRecords($p, int &$total): array {
        $total = 0;
        if ($p->offset == '') {
            $p->offset = 0;
        }
        if ($p->limit == '') {
            $p->limit = 20;
        }
        $filter = new Filter('tasks','t');
        $filter->setColumns('t.id, t.description, t.tasktype, t.state, t.deadline, t.nick, t.sequence');
        if ($p->searchstr != '') {
            $filter->where(['t.description','like','%'.$filter->quote($p->searchstr).'%']);
        }
        if ($p->filterState != '') {
            $filter->where(['t.state','=',$p->filterState]);
        }
        if ($p->project_id != '') {
            $filter->where(['t.project_id','=',$p->project_id]);
        }
        $filter->order($p->order.' '.$p->order_dir);
        $filter->offset($p->offset);
        $filter->limit($p->limit);
        $total = $filter->count();
        return $filter->get();
    }
    
    /**
     * rekord tárolása sequence kezeléssel
     * @param TaskRecord
     * @return bool
     */ 
    public function save(&$task): bool {
      $this->errorMsg = '';
      $db = new DB();
      $table = new table('tasks');
      if ($task->id == 0) {
         // state -ben a >= sequence - k növelése
         $db->exec('update tasks
         set sequence = sequence + 1
         where project_id = "'.$db->quote($task->project_id).'" and
         state = "'.$db->quote($task->state).'" and
         sequence >= '.$db->quote($task->sequence));
         $table->insert($task);
         $this->errorMsg = $table->getErrorMsg();
         $task->id = $table->getInsertedId();
      } else {  
         $table->where(['id','=',$task->id]); 
         $oldTask = $table->first();
         if (($task->state != $oldTask->state) | ($task->sequence != $oldTask->sequence)) {
            $db->exec('update tasks
            set sequence = sequence - 1
            where project_id = '.$db->quote($task->project_id).' and
            state = '.$db->quote($oldTask->state).' and
            sequence > '.$db->quote($oldTask->sequence));
            $db->exec('update tasks
            set sequence = sequence + 1
            where project_id = '.$db->quote($task->project_id).' and
            state = '.$db->quote($task->state).' and
            sequence >= '.$db->quote($task->sequence));
         }
         $table->update($task);
         $this->errorMsg = $table->getErrorMsg();
      }
      return ($this->errorMsg == '');   
    }
    
    /**
     * get last task
     * @param int $project_id
     * @param string $state
     * @return TaskRecord
     */
    public function getLast(int $project_id, string $state) {
        $table = new Table('tasks');
        $table->where(['project_id','=',$project_id]);
        $table->where(['state','=',$state]);
        $table->order('sequence');
        $tasks = $table->get();
        if (count($tasks) > 0) {
            $result = $tasks[count($tasks) - 1];
        } else {
            $result = new TaskRecord();
        }
        return $result;
    }
} // class
?>
