<?php
use Ruckusing\Migration\Base as Ruckusing_Migration_Base;

class RenameSettingsColumns extends Ruckusing_Migration_Base
{
    /**
     * @var string
     */
    protected $tablename = 'directus_settings';

    /**
     * @var array
     */
    protected $columns = [
        'collection' => 'scope',
        'name' => 'key'
    ];

    public function up()
    {
        foreach ($this->columns as $oldColumn => $newColumn) {
            if (!$this->has_column($this->tablename, $newColumn) && $this->has_column($this->tablename, $oldColumn)) {
                $this->rename_column($this->tablename, $oldColumn, $newColumn);
            }
        }
    }//up()

    public function down()
    {
        foreach ($this->columns as $oldColumn => $newColumn) {
            if ($this->has_column($this->tablename, $newColumn) && !$this->has_column($this->tablename, $oldColumn)) {
                $this->rename_column($this->tablename, $newColumn, $oldColumn);
            }
        }
    }//down()
}
