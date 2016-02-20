<?php
namespace Concrete\Controller\Dialog\Tree\Node;

use Concrete\Controller\Backend\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Delete extends Permissions
{
    protected $viewPath = '/dialogs/tree/node/delete';

    protected function canAccess()
    {
        $node = $this->getNode();
        $np = new \Permissions($node);
        return $np->canDeleteTreeNode();
    }

    public function remove_tree_node()
    {
        $node = $this->getNode();
        $tree = $node->getTreeObject();
        $treeNodeID = $node->getTreeNodeID();
        $error = \Core::make('error');
        if (!\Core::make('token')->validate("remove_tree_node")) {
            $error->add(\Core::make('token')->getErrorMessage());
        }
        if (!is_object($node)) {
            $error->add(t('Invalid node.'));
        }

        if ($node->getTreeNodeParentID() == 0) {
            $error->add(t('You may not remove the top level node.'));
        }

        if (!$error->has()) {
            $node->delete();
            $r = new \stdClass();
            $r->treeNodeID = $treeNodeID;
            return new JsonResponse($r);
        } else {
            return new JsonResponse($error);
        }

    }

}
