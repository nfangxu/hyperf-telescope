<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Wind\Telescope\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

use Wind\Telescope\Model\TelescopeEntryModel;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\View\RenderInterface;


abstract class EntryController
{
    /**
     * The entry type for the controller.
     *
     * @return string
     */
    abstract protected function entryType();

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    abstract protected function watcher();

    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;


    public function index()
    {
        if (!$this->request->has('before')) {
            return '';
        }
        $before = $this->request->input('before');
        $limit = $this->request->input('take', 50);
        $query = TelescopeEntryModel::where('type', $this->entryType())->orderByDesc('sequence');

        if ($before) {
            $query->where('sequence', '<', $before);
        }

        $entries = $query->limit($limit)->get()->toArray();

        foreach ($entries as &$item) {
            if (isset($item['content']['response'])) {
                $item['content']['response'] = '';
            }
        }

        return $this->response->json([
            'entries' => $entries,
            'status' => $this->status(),
        ]);
    }


    public function show($id)
    {
        $entry = TelescopeEntryModel::find($id);
        $entry->tags = [];
        $batch = TelescopeEntryModel::where('batch_id', $entry->batch_id)->orderByDesc('sequence')->get();

        return $this->response->json([
            'entry' => $entry,
            'batch' => $batch,
        ]);
    }

    /**
     * Determine the watcher recording status.
     *
     * @return string
     */
    protected function status()
    {
        // if (! config('telescope.enabled', false)) {
        //     return 'disabled';
        // }

        // if (cache('telescope:pause-recording', false)) {
        //     return 'paused';
        // }

        // $watcher = config('telescope.watchers.'.$this->watcher());

        // if (! $watcher || (isset($watcher['enabled']) && ! $watcher['enabled'])) {
        //     return 'off';
        // }

        return 'enabled';
    }
}
