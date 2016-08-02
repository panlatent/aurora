<?php
/**
 * Aurora - A HTTP Application Server of PHP Script
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/aurora
 * @license https://opensource.org/licenses/MIT
 */

namespace Aurora\Http;

/**
 * Class Server
 * @package Aurora\Http
 */
class Server extends \Aurora\Server
{
    /**
     * @var \Aurora\Http\Pipeline
     */
    protected $pipeline;

    public function __construct()
    {
        parent::__construct();

        $this->pipeline->setEventManager($this->event);
        $this->event->bind('pipeline:append', $this->pipeline);
        $this->pipeline->pipe([$this, 'request']);
    }

    public function request(Request $request, Pipeline $pipe)
    {
        if ($request->version(true) == '1.1') {
            if ($request->header('HTTP_CONNECTION', true) == 'close') {
                $this->event->bind('pipeline:dispatch', function() use($pipe) {
                    $pipe->client->close();
                    $this->event->base()->stop();
                });
            }
        }
    }

    protected function initPipeline()
    {
        $this->pipeline = new Pipeline();
    }
}