<?php
/**
 * GroupBy used for viewing aggregate Value Analytics grant role data by month.
 */

namespace DataWarehouse\Query\ValueAnalyticsGrantRoles\GroupBys;

use DataWarehouse\Query\Model\FormulaField;
use DataWarehouse\Query\Model\OrderBy;
use DataWarehouse\Query\Model\Schema;
use DataWarehouse\Query\Model\Table;
use DataWarehouse\Query\Model\TableField;
use DataWarehouse\Query\Query;
use DataWarehouse\Query\ValueAnalyticsGrantRoles\GroupBy;

class GroupByMonth extends GroupBy
{
    public function __construct()
    {
        parent::__construct(
            'month',
            array(),
            "
                SELECT DISTINCT
                    gt.id,
                    DATE(gt.month_start) AS long_name,
                    DATE(gt.month_start) AS short_name,
                    gt.month_start_ts AS start_ts
                FROM modw.months gt
                WHERE 1
                ORDER BY gt.id ASC
            ",
            array()
        );
        $this->setAvailableOnDrilldown(false);
    }

    public static function getLabel()
    {
        return 'Month';
    }

    public function applyTo(Query &$query, Table $dataTable, $multiGroup = false)
    {
        $modwSchema = new Schema('modw');
        $modwAggregatesSchema = new Schema('modw_aggregates');

        $idField = new TableField(
            $query->getDataTable(),
            "month_id",
            $this->getIdColumnName($multiGroup)
        );
        $nameField = new FormulaField(
            'date('.$query->getDateTable()->getAlias().".month_start)",
            $this->getLongNameColumnName($multiGroup)
        );
        $shortnameField = new FormulaField(
            'date('.$query->getDateTable()->getAlias().".month_start)",
            $this->getShortNameColumnName($multiGroup)
        );
        $valueField = new TableField(
            $query->getDateTable(),
            "month_start_ts"
        );
        $query->addField($idField);
        $query->addField($nameField);
        $query->addField($shortnameField);
        $query->addField($valueField);

        $query->addGroup($idField);

        $this->addOrder($query, $multiGroup);
    }

    public function addOrder(
        Query &$query,
        $multiGroup = false,
        $dir = 'asc',
        $prepend = false
    ) {
        $orderField = new OrderBy(
            new TableField($query->getDataTable(), "month_id"),
            $dir,
            $this->getName()
        );
        if ($prepend === true) {
            $query->prependOrder($orderField);
        } else {
            $query->addOrder($orderField);
        }
    }

    public function pullQueryParameters(&$request)
    {
        return array();
    }

    public function pullQueryParameterDescriptions(&$request)
    {
        return array();
    }
}
