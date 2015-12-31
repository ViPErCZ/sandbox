<?php
namespace Component\Permission;

use Component\Base\WithLogged\BaseControl;
use Model\Permission\ActionRepository;
use Model\Permission\Entity\ActionEntity;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Form;
use Nette\Security\User;

class ActionForm extends BaseControl {

    /**@var \Model\Permission\ActionRepository */
    private $actionRepository;

    /**@var int */
    private $aclActionID;

    /** @var User */
    private $user;

    /** messages */
    const PERMISSION = "Na tuto operaci nemáte dostatečná oprávnění";

    /**
     * @param ActionRepository $actionRepository
     * @param User $user
     */
    public function __construct(ActionRepository $actionRepository, User $user) {
        parent::__construct();

        $this->actionRepository = $actionRepository;
        $this->aclActionID = NULL;
        $this->user = $user;
    }

    /**
     * @param int $aclActionID
     */
    public function setAclActionID($aclActionID) {
        $this->aclActionID = $aclActionID;
    }

    /** Render
     *
     */
    public function render() {
        $template = $this->template;
        $template->setFile(__DIR__ . "/latte/actionForm.latte");

        $template->render();
    }

    /** Submit
     *
     * @param \Nette\Application\UI\Form $form
     */
    public function Submit(Form $form) {
        $json = new \stdClass();
        $json->result = "success";
        $values = $form->getValues();

        if (!empty($values['aclActionID'])) {
            if ($this->user->isAllowed("permission", "edit")) {
                $actionEntity = $this->actionRepository->get($values['aclActionID']);
                if ($actionEntity) {
                    $actionEntity->setName($values['name']);
                    $actionEntity->setHumanName($values['humanName']);
                    try {
                        $result = $this->actionRepository->save();
                    } catch (\PDOException $e) {
                        $result = $e->getMessage();
                    }
                } else {
                    $result = FALSE;
                }
            } else {
                $result = ActionForm::PERMISSION;
            }
        } else {
            if ($this->user->isAllowed("permission", "add")) {
                $actionEntity = new ActionEntity();
                $actionEntity->setName($values['name']);
                $actionEntity->setHumanName($values['humanName']);

                try {
                    $ent = $this->actionRepository->push($actionEntity)->save();
                    if ($ent instanceof ActionEntity || $ent === TRUE) {
                        $result = TRUE;
                    } else {
                        $result = FALSE;
                    }
                } catch (\PDOException $e) {
                    $result = $e->getMessage();
                }
            } else {
                $result = ActionForm::PERMISSION;
            }
        }

        if ($result === TRUE) {
            $json->result = "success";
        } else {
            $json->result = "error";
            $json->message = $result;
        }
        $response = new JsonResponse($json);
        $this->getPresenter()->sendResponse($response);
	}

	/** Error Submit
	 * @param Form $form
	 */
	public function Error(Form $form) {
		$json = new \stdClass();
		$json->result = "error";
		$json->message = implode("<br />", $form->getErrors());
		$json->notify = implode(",", $form->getErrors());
		$response = new JsonResponse($json);
		$this->getPresenter()->sendResponse($response);
	}

    /**
     * @return \Nette\Application\UI\Form
     */
    protected function createComponentForm() {
        $form = new Form;
        $form->getElementPrototype()->id = "actionForm";
        $form->addText("name", "Jméno akce:")
            ->setHtmlId("name")
            ->setRequired("Prosím zadejte jméno akce.");
        $form->addText("humanName", "Lidský formát akce:")
            ->setHtmlId("humanName")
            ->setRequired("Prosím zadejte jméno akce v lidském formátu.");
        $form->addHidden("aclActionID");
		$form->addButton("cancel", "Storno")->setHtmlId("cancel");
		$form->addSubmit("sender", "Uložit změny")->setHtmlId("sender");

        if ($this->aclActionID) {
            $actionEntity = $this->actionRepository->get($this->aclActionID);
            if ($actionEntity) {
                $form['aclActionID']->setValue($actionEntity->aclActionID);
                $form['name']->setValue($actionEntity->name);
                $form['humanName']->setValue($actionEntity->humanName);
            }
        }

        $form->onSuccess[] = callback($this, "Submit");
		$form->onError[] = callback($this, "Error");

        return $form;
    }

}