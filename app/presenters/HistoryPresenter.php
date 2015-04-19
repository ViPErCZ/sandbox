<?php
/**
 * User: Martin
 * Date: 12.11.13
 * Time: 12:58
 */

namespace App;

use Model\History\HistoryRepository;
use Nette\Forms\Container;
use Nextras\Datagrid\MyDatagrid;

class HistoryPresenter extends BasePresenter {

	/** @var \Model\History\HistoryRepository */
	private $historyRepository;

	/** Inject
	 * @param \Model\History\HistoryRepository $historyRepository
	 */
	public function injectHistoryRepository(HistoryRepository $historyRepository) {
		$this->historyRepository = $historyRepository;
	}

	/**
	 * @return \Nextras\Datagrid\Datagrid
	 */
	protected function createComponentGrid() {
		$grid = new MyDatagrid();
		$grid->setTranslator($this->translator);
		$grid->setShowCheckboxes(FALSE);
		$grid->addColumn('login', 'Uživatel')->enableSort();
		$grid->addColumn('ip', 'IP adresa')->enableSort();
		$grid->addColumn('message', 'Zpráva')->enableSort();
		$grid->addColumn('timestamp', 'Datum a čas')->enableSort();
		$grid->setRowPrimaryKey('syslogID');

		$model = $this->historyRepository;
		$grid->setColumnGetterCallback(function($row, $column) {
			$getter = "get" . ucfirst($column);
			return $row->$getter();
		});
		$grid->setDatasourceCallback(function($filter, $order, $paginator) use ($model) {
			$data = $model->read($paginator)->select("*, user.login");

			foreach ($filter as $k => $v) {
				$k = str_replace("login", "user.login", $k);
				$k = str_replace("ip", "syslog.ip", $k);
				$data->where($k . " LIKE ?", "%".$v."%");
			}
			if ($order) {
				$order = str_replace("login", "user.login", implode(" ", $order));
				$order = str_replace("ip", "syslog.ip", $order);;
				$data->order($order);
			} else {
				$data->order("timestamp DESC");
			}
			return $data;
		});
		$grid->setPagination(10, function($filter) use ($model) {
			$data = $model->read();
			foreach ($filter as $k => $v) {
				$k = str_replace("login", "user.login", $k);
				$k = str_replace("ip", "syslog.ip", $k);
				$data->where($k . " LIKE ?", "%".$v."%");
			}
			return $data->count('*');
		});
		$grid->setFilterFormFactory(function() {
			$form = new Container();
			$form->addText("login", "Uživatel");
			$form->addText("ip", "IP adresa");
			$form->addText("message", "Zpráva");

			// these buttons are not compulsory
			$form->addSubmit('filter', 'Filtrovat')->getControlPrototype()->class = 'btn btn-primary';
			$form->addSubmit('cancel', 'Storno')->getControlPrototype()->class = 'btn';

			return $form;
		});

		return $grid;
	}
} 