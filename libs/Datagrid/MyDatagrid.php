<?php
/**
 * Created by PhpStorm.
 * User: viper
 * Date: 20.6.14
 * Time: 14:00
 */

namespace Nextras\Datagrid;

/**
 * Class MyDatagrid
 * @package Nextras\Datagrid
 */
class MyDatagrid extends Datagrid {

	/** @var bool */
	protected $showCheckboxes = false;

	/**
	 * @param bool $showCheckboxes
	 */
	public function setShowCheckboxes($showCheckboxes = TRUE) {
		$this->showCheckboxes = $showCheckboxes;
	}

	/**
	 * @throws \RuntimeException
	 */
	public function render()
	{
		if ($this->filterFormFactory) {
			$this['form']['filter']->setDefaults($this->filter);
		}

		$this->template->data = $this->getData();
		$this->template->columns = $this->columns;
		$this->template->editRowKey = $this->editRowKey;
		$this->template->rowPrimaryKey = $this->rowPrimaryKey;
		$this->template->paginator = $this->paginator;
		$this->template->showCheckboxes = $this->showCheckboxes;

		foreach ($this->cellsTemplates as &$cellsTemplate) {
			if ($cellsTemplate instanceof IFileTemplate) {
				$cellsTemplate = $cellsTemplate->getFile();
			}
			if (!file_exists($cellsTemplate)) {
				throw new \RuntimeException("Cells template '{$cellsTemplate}' does not exist.");
			}
		}

		$this->template->cellsTemplates = $this->cellsTemplates;
		$this->template->showFilterCancel = $this->filterDataSource != $this->filterDefaults; // @ intentionaly
		$this->template->setFile(__DIR__ . '/myDatagrid.latte');
		$this->template->render();
	}
} 