<?php

namespace Isais;

// For environments without Composer installed.
require_once __DIR__ . '/AutoLoader.php';

use Isais\Context\Context;
use Isais\EntryPoint\EntryPoint;
use Isais\IsaisServiceContainer;

class Isais
{
    private $services = null;

    public function __construct() {
        $this->services = new IsaisServiceContainer();
        $this->services->loadWiringFile(__DIR__ . '/ServiceWiring.php');
    }

    public function run($entry_point)
    {
        $context = $this->services->getContext();
        $request_method = $context->getRequestMethod();

        # Multiple DBs or commits might be used; keep the request as transactional as possible
        if (
            isset($request_method) &&
            $request_method === 'POST'
        ) {
            ignore_user_abort(true);
        }

        $context->setContext(Context::CONTEXT_ENTRY_POINT, $entry_point);
        $this->services
            ->getEntryPoint()
            ->execute();
    }
}
