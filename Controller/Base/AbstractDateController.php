<?php
/**
 * Almanac.
 *
 * @copyright Ralf Koester (RK)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Ralf Koester <ralf@familie-koester.de>.
 * @link http://k62.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.1 (https://modulestudio.de).
 */

namespace RK\AlmanacModule\Controller\Base;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Component\SortableColumns\Column;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\Response\PlainResponse;
use Zikula\Core\RouteUrl;
use Zikula\UsersModule\Constant\UsersConstant;
use RK\AlmanacModule\Entity\DateEntity;
use RK\AlmanacModule\Helper\FeatureActivationHelper;

/**
 * Date controller base class.
 */
abstract class AbstractDateController extends AbstractController
{
    /**
     * This is the default action handling the index admin area called without defining arguments.
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminIndexAction(Request $request)
    {
        return $this->indexInternal($request, true);
    }
    
    /**
     * This is the default action handling the index area called without defining arguments.
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        return $this->indexInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminIndex() and index().
     */
    protected function indexInternal(Request $request, $isAdmin = false)
    {
        // parameter specifying which type of objects we are treating
        $objectType = 'date';
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        if (!$this->hasPermission('RKAlmanacModule:' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        return $this->redirectToRoute('rkalmanacmodule_date_' . $templateParameters['routeArea'] . 'view');
    }
    /**
     * This action provides an item list overview in the admin area.
     *
     * @param Request $request Current request instance
     * @param string $sort         Sorting field
     * @param string $sortdir      Sorting direction
     * @param int    $pos          Current pager position
     * @param int    $num          Amount of entries to display
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminViewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, true);
    }
    
    /**
     * This action provides an item list overview.
     *
     * @param Request $request Current request instance
     * @param string $sort         Sorting field
     * @param string $sortdir      Sorting direction
     * @param int    $pos          Current pager position
     * @param int    $num          Amount of entries to display
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function viewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, false);
    }
    
    /**
     * This method includes the common implementation code for adminView() and view().
     */
    protected function viewInternal(Request $request, $sort, $sortdir, $pos, $num, $isAdmin = false)
    {
        // parameter specifying which type of objects we are treating
        $objectType = 'date';
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        if (!$this->hasPermission('RKAlmanacModule:' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        $controllerHelper = $this->get('rk_almanac_module.controller_helper');
        $viewHelper = $this->get('rk_almanac_module.view_helper');
        
        $request->query->set('sort', $sort);
        $request->query->set('sortdir', $sortdir);
        $request->query->set('pos', $pos);
        
        $sortableColumns = new SortableColumns($this->get('router'), 'rkalmanacmodule_date_' . ($isAdmin ? 'admin' : '') . 'view', 'sort', 'sortdir');
        
        $sortableColumns->addColumns([
            new Column('workflowState'),
            new Column('dateTitle'),
            new Column('dateDescription'),
            new Column('allDay'),
            new Column('allDayDate'),
            new Column('startDate'),
            new Column('endDate'),
            new Column('dateImage'),
            new Column('dateUrl'),
            new Column('createdBy'),
            new Column('createdDate'),
            new Column('updatedBy'),
            new Column('updatedDate'),
        ]);
        
        $templateParameters = $controllerHelper->processViewActionParameters($objectType, $sortableColumns, $templateParameters, true);
        
        // filter by permissions
        $filteredEntities = [];
        foreach ($templateParameters['items'] as $date) {
            if (!$this->hasPermission('RKAlmanacModule:' . ucfirst($objectType) . ':', $date->getKey() . '::', $permLevel)) {
                continue;
            }
            $filteredEntities[] = $date;
        }
        $templateParameters['items'] = $filteredEntities;
        
        // filter by category permissions
        $featureActivationHelper = $this->get('rk_almanac_module.feature_activation_helper');
        if ($featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
            $templateParameters['items'] = $this->get('rk_almanac_module.category_helper')->filterEntitiesByPermission($templateParameters['items']);
        }
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($objectType, 'view', $templateParameters);
    }
    /**
     * This action provides a item detail view in the admin area.
     *
     * @param Request $request Current request instance
     * @param DateEntity $date Treated date instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if date to be displayed isn't found
     */
    public function adminDisplayAction(Request $request, DateEntity $date)
    {
        return $this->displayInternal($request, $date, true);
    }
    
    /**
     * This action provides a item detail view.
     *
     * @param Request $request Current request instance
     * @param DateEntity $date Treated date instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if date to be displayed isn't found
     */
    public function displayAction(Request $request, DateEntity $date)
    {
        return $this->displayInternal($request, $date, false);
    }
    
    /**
     * This method includes the common implementation code for adminDisplay() and display().
     */
    protected function displayInternal(Request $request, DateEntity $date, $isAdmin = false)
    {
        // parameter specifying which type of objects we are treating
        $objectType = 'date';
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        if (!$this->hasPermission('RKAlmanacModule:' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        // create identifier for permission check
        $instanceId = $date->getKey();
        if (!$this->hasPermission('RKAlmanacModule:' . ucfirst($objectType) . ':', $instanceId . '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        
        if ($date->getWorkflowState() != 'approved' && !$this->hasPermission('RKAlmanacModule:' . ucfirst($objectType) . ':', $instanceId . '::', ACCESS_ADMIN)) {
            throw new AccessDeniedException();
        }
        
        $featureActivationHelper = $this->get('rk_almanac_module.feature_activation_helper');
        if ($featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
            if (!$this->get('rk_almanac_module.category_helper')->hasPermission($date)) {
                throw new AccessDeniedException();
            }
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : '',
            $objectType => $date
        ];
        
        $controllerHelper = $this->get('rk_almanac_module.controller_helper');
        $templateParameters = $controllerHelper->processDisplayActionParameters($objectType, $templateParameters, true);
        
        // fetch and return the appropriate template
        $response = $this->get('rk_almanac_module.view_helper')->processTemplate($objectType, 'display', $templateParameters);
        
        return $response;
    }
    /**
     * This action provides a handling of edit requests in the admin area.
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by form handler if date to be edited isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function adminEditAction(Request $request)
    {
        return $this->editInternal($request, true);
    }
    
    /**
     * This action provides a handling of edit requests.
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by form handler if date to be edited isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function editAction(Request $request)
    {
        return $this->editInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminEdit() and edit().
     */
    protected function editInternal(Request $request, $isAdmin = false)
    {
        // parameter specifying which type of objects we are treating
        $objectType = 'date';
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        if (!$this->hasPermission('RKAlmanacModule:' . ucfirst($objectType) . ':', '::', $permLevel)) {
            throw new AccessDeniedException();
        }
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        $controllerHelper = $this->get('rk_almanac_module.controller_helper');
        $templateParameters = $controllerHelper->processEditActionParameters($objectType, $templateParameters);
        
        // delegate form processing to the form handler
        $formHandler = $this->get('rk_almanac_module.form.handler.date');
        $result = $formHandler->processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        
        $templateParameters = $formHandler->getTemplateParameters();
        
        // fetch and return the appropriate template
        return $this->get('rk_almanac_module.view_helper')->processTemplate($objectType, 'edit', $templateParameters);
    }
    /**
     * This action provides a handling of simple delete requests in the admin area.
     *
     * @param Request $request Current request instance
     * @param DateEntity $date Treated date instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if date to be deleted isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function adminDeleteAction(Request $request, DateEntity $date)
    {
        return $this->deleteInternal($request, $date, true);
    }
    
    /**
     * This action provides a handling of simple delete requests.
     *
     * @param Request $request Current request instance
     * @param DateEntity $date Treated date instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown by param converter if date to be deleted isn't found
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function deleteAction(Request $request, DateEntity $date)
    {
        return $this->deleteInternal($request, $date, false);
    }
    
    /**
     * This method includes the common implementation code for adminDelete() and delete().
     */
    protected function deleteInternal(Request $request, DateEntity $date, $isAdmin = false)
    {
        // parameter specifying which type of objects we are treating
        $objectType = 'date';
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_DELETE;
        if (!$this->hasPermission('RKAlmanacModule:' . ucfirst($objectType) . ':', '::', $permLevel)) {
            if ($isAdmin) {
                throw new AccessDeniedException();
            }
            $currentUserApi = $this->get('zikula_users_module.current_user');
            $currentUserId = $currentUserApi->isLoggedIn() ? $currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
            $isOwner = $currentUserId > 0 && null !== $date->getCreatedBy() && $currentUserId == $date->getCreatedBy()->getUid();
            if (!$isOwner || !$this->hasPermission('RKAlmanacModule:' . ucfirst($objectType) . ':', '::', ACCESS_EDIT)) {
                throw new AccessDeniedException();
            }
        }
        $logger = $this->get('logger');
        $logArgs = ['app' => 'RKAlmanacModule', 'user' => $this->get('zikula_users_module.current_user')->get('uname'), 'entity' => 'date', 'id' => $date->getKey()];
        
        // determine available workflow actions
        $workflowHelper = $this->get('rk_almanac_module.workflow_helper');
        $actions = $workflowHelper->getActionsForObject($date);
        if (false === $actions || !is_array($actions)) {
            $this->addFlash('error', $this->__('Error! Could not determine workflow actions.'));
            $logger->error('{app}: User {user} tried to delete the {entity} with id {id}, but failed to determine available workflow actions.', $logArgs);
            throw new \RuntimeException($this->__('Error! Could not determine workflow actions.'));
        }
        
        // redirect to the list of dates
        $redirectRoute = 'rkalmanacmodule_date_' . ($isAdmin ? 'admin' : '') . 'view';
        
        // check whether deletion is allowed
        $deleteActionId = 'delete';
        $deleteAllowed = false;
        foreach ($actions as $actionId => $action) {
            if ($actionId != $deleteActionId) {
                continue;
            }
            $deleteAllowed = true;
            break;
        }
        if (!$deleteAllowed) {
            $this->addFlash('error', $this->__('Error! It is not allowed to delete this date.'));
            $logger->error('{app}: User {user} tried to delete the {entity} with id {id}, but this action was not allowed.', $logArgs);
        
            return $this->redirectToRoute($redirectRoute);
        }
        
        $form = $this->createForm('Zikula\Bundle\FormExtensionBundle\Form\Type\DeletionType', $date);
        $hookHelper = $this->get('rk_almanac_module.hook_helper');
        
        // Call form aware display hooks
        $formHook = $hookHelper->callFormDisplayHooks($form, $date, FormAwareCategory::TYPE_DELETE);
        
        if ($form->handleRequest($request)->isValid()) {
            if ($form->get('delete')->isClicked()) {
                // Let any ui hooks perform additional validation actions
                $validationErrors = $hookHelper->callValidationHooks($date, UiHooksCategory::TYPE_VALIDATE_DELETE);
                if (count($validationErrors) > 0) {
                    foreach ($validationErrors as $message) {
                        $this->addFlash('error', $message);
                    }
                } else {
                    // execute the workflow action
                    $success = $workflowHelper->executeAction($date, $deleteActionId);
                    if ($success) {
                        $this->addFlash('status', $this->__('Done! Item deleted.'));
                        $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', $logArgs);
                    }
                    
                    // Call form aware processing hooks
                    $hookHelper->callFormProcessHooks($form, $date, FormAwareCategory::TYPE_PROCESS_DELETE);
                    
                    // Let any ui hooks know that we have deleted the date
                    $hookHelper->callProcessHooks($date, UiHooksCategory::TYPE_PROCESS_DELETE);
                    
                    return $this->redirectToRoute($redirectRoute);
                }
            } elseif ($form->get('cancel')->isClicked()) {
                $this->addFlash('status', $this->__('Operation cancelled.'));
        
                return $this->redirectToRoute($redirectRoute);
            }
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : '',
            'deleteForm' => $form->createView(),
            $objectType => $date,
            'formHookTemplates' => $formHook->getTemplates()
        ];
        
        $controllerHelper = $this->get('rk_almanac_module.controller_helper');
        $templateParameters = $controllerHelper->processDeleteActionParameters($objectType, $templateParameters, true);
        
        // fetch and return the appropriate template
        return $this->get('rk_almanac_module.view_helper')->processTemplate($objectType, 'delete', $templateParameters);
    }

    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param Request $request Current request instance
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function adminHandleSelectedEntriesAction(Request $request)
    {
        return $this->handleSelectedEntriesActionInternal($request, true);
    }
    
    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param Request $request Current request instance
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function handleSelectedEntriesAction(Request $request)
    {
        return $this->handleSelectedEntriesActionInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminHandleSelectedEntriesAction() and handleSelectedEntriesAction().
     *
     * @param Request $request Current request instance
     * @param boolean $isAdmin Whether the admin area is used or not
     */
    protected function handleSelectedEntriesActionInternal(Request $request, $isAdmin = false)
    {
        $objectType = 'date';
        
        // Get parameters
        $action = $request->request->get('action', null);
        $items = $request->request->get('items', null);
        
        $action = strtolower($action);
        
        $repository = $this->get('rk_almanac_module.entity_factory')->getRepository($objectType);
        $workflowHelper = $this->get('rk_almanac_module.workflow_helper');
        $hookHelper = $this->get('rk_almanac_module.hook_helper');
        $logger = $this->get('logger');
        $userName = $this->get('zikula_users_module.current_user')->get('uname');
        
        // process each item
        foreach ($items as $itemId) {
            // check if item exists, and get record instance
            $entity = $repository->selectById($itemId, false);
            if (null === $entity) {
                continue;
            }
        
            // check if $action can be applied to this entity (may depend on it's current workflow state)
            $allowedActions = $workflowHelper->getActionsForObject($entity);
            $actionIds = array_keys($allowedActions);
            if (!in_array($action, $actionIds)) {
                // action not allowed, skip this object
                continue;
            }
        
            // Let any ui hooks perform additional validation actions
            $hookType = $action == 'delete' ? UiHooksCategory::TYPE_VALIDATE_DELETE : UiHooksCategory::TYPE_VALIDATE_EDIT;
            $validationErrors = $hookHelper->callValidationHooks($entity, $hookType);
            if (count($validationErrors) > 0) {
                foreach ($validationErrors as $message) {
                    $this->addFlash('error', $message);
                }
                continue;
            }
        
            $success = false;
            try {
                // execute the workflow action
                $success = $workflowHelper->executeAction($entity, $action);
            } catch (\Exception $exception) {
                $this->addFlash('error', $this->__f('Sorry, but an error occured during the %action% action.', ['%action%' => $action]) . '  ' . $exception->getMessage());
                $logger->error('{app}: User {user} tried to execute the {action} workflow action for the {entity} with id {id}, but failed. Error details: {errorMessage}.', ['app' => 'RKAlmanacModule', 'user' => $userName, 'action' => $action, 'entity' => 'date', 'id' => $itemId, 'errorMessage' => $exception->getMessage()]);
            }
        
            if (!$success) {
                continue;
            }
        
            if ($action == 'delete') {
                $this->addFlash('status', $this->__('Done! Item deleted.'));
                $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', ['app' => 'RKAlmanacModule', 'user' => $userName, 'entity' => 'date', 'id' => $itemId]);
            } else {
                $this->addFlash('status', $this->__('Done! Item updated.'));
                $logger->notice('{app}: User {user} executed the {action} workflow action for the {entity} with id {id}.', ['app' => 'RKAlmanacModule', 'user' => $userName, 'action' => $action, 'entity' => 'date', 'id' => $itemId]);
            }
        
            // Let any ui hooks know that we have updated or deleted an item
            $hookType = $action == 'delete' ? UiHooksCategory::TYPE_PROCESS_DELETE : UiHooksCategory::TYPE_PROCESS_EDIT;
            $url = null;
            if ($action != 'delete') {
                $urlArgs = $entity->createUrlArgs();
                $urlArgs['_locale'] = $request->getLocale();
                $url = new RouteUrl('rkalmanacmodule_date_display', $urlArgs);
            }
            $hookHelper->callProcessHooks($entity, $hookType, $url);
        }
        
        return $this->redirectToRoute('rkalmanacmodule_date_' . ($isAdmin ? 'admin' : '') . 'index');
    }

    /**
     * This method cares for a redirect within an inline frame.
     *
     * @param string  $idPrefix    Prefix for inline window element identifier
     * @param string  $commandName Name of action to be performed (create or edit)
     * @param integer $id          Identifier of created date (used for activating auto completion after closing the modal window)
     *
     * @return PlainResponse Output
     */
    public function handleInlineRedirectAction($idPrefix, $commandName, $id = 0)
    {
        if (empty($idPrefix)) {
            return false;
        }
        
        $formattedTitle = '';
        $searchTerm = '';
        if (!empty($id)) {
            $repository = $this->get('rk_almanac_module.entity_factory')->getRepository('date');
            $date = $repository->selectById($id);
            if (null !== $date) {
                $formattedTitle = $this->get('rk_almanac_module.entity_display_helper')->getFormattedTitle($date);
                $searchTerm = $date->getDateTitle();
            }
        }
        
        $templateParameters = [
            'itemId' => $id,
            'formattedTitle' => $formattedTitle,
            'searchTerm' => $searchTerm,
            'idPrefix' => $idPrefix,
            'commandName' => $commandName
        ];
        
        return new PlainResponse($this->get('twig')->render('@RKAlmanacModule/Date/inlineRedirectHandler.html.twig', $templateParameters));
    }
}