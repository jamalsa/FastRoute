<?php

namespace FastRoute;

class RouteCollectorTest extends \PHPUnit_Framework_TestCase {
    public function testShortcuts() {
        $r = new DummyRouteCollector();

        $r->delete('/delete', 'delete');
        $r->get('/get', 'get');
        $r->head('/head', 'head');
        $r->patch('/patch', 'patch');
        $r->post('/post', 'post');
        $r->put('/put', 'put');

        $expected = array(
            array('DELETE', '/delete', 'delete'),
            array('GET', '/get', 'get'),
            array('HEAD', '/head', 'head'),
            array('PATCH', '/patch', 'patch'),
            array('POST', '/post', 'post'),
            array('PUT', '/put', 'put'),
        );

        $this->assertSame($expected, $r->routes);
    }

    public function testGroups() {
        $r = new DummyRouteCollector();

        $r->delete('/delete', 'delete');
        $r->get('/get', 'get');
        $r->head('/head', 'head');
        $r->patch('/patch', 'patch');
        $r->post('/post', 'post');
        $r->put('/put', 'put');

        $r->addGroup('/group-one', function (DummyRouteCollector $r) {
            $r->delete('/delete', 'delete');
            $r->get('/get', 'get');
            $r->head('/head', 'head');
            $r->patch('/patch', 'patch');
            $r->post('/post', 'post');
            $r->put('/put', 'put');

            $r->addGroup('/group-two', function (DummyRouteCollector $r) {
                $r->delete('/delete', 'delete');
                $r->get('/get', 'get');
                $r->head('/head', 'head');
                $r->patch('/patch', 'patch');
                $r->post('/post', 'post');
                $r->put('/put', 'put');
            });
        });

        $r->addGroup('/admin', function (DummyRouteCollector $r) {
            $r->get('-some-info', 'admin-some-info');
        });
        $r->addGroup('/admin-', function (DummyRouteCollector $r) {
            $r->get('more-info', 'admin-more-info');
        });

        $expected = array(
            array('DELETE', '/delete', 'delete'),
            array('GET', '/get', 'get'),
            array('HEAD', '/head', 'head'),
            array('PATCH', '/patch', 'patch'),
            array('POST', '/post', 'post'),
            array('PUT', '/put', 'put'),
            array('DELETE', '/group-one/delete', 'delete'),
            array('GET', '/group-one/get', 'get'),
            array('HEAD', '/group-one/head', 'head'),
            array('PATCH', '/group-one/patch', 'patch'),
            array('POST', '/group-one/post', 'post'),
            array('PUT', '/group-one/put', 'put'),
            array('DELETE', '/group-one/group-two/delete', 'delete'),
            array('GET', '/group-one/group-two/get', 'get'),
            array('HEAD', '/group-one/group-two/head', 'head'),
            array('PATCH', '/group-one/group-two/patch', 'patch'),
            array('POST', '/group-one/group-two/post', 'post'),
            array('PUT', '/group-one/group-two/put', 'put'),
            array('GET', '/admin-some-info', 'admin-some-info'),
            array('GET', '/admin-more-info', 'admin-more-info'),
        );

        $this->assertSame($expected, $r->routes);
    }
}

class DummyRouteCollector extends RouteCollector {
    public $routes = array();
    public function __construct() {}
    public function addRoute($method, $route, $handler) {
        $route = $this->currentGroupPrefix . $route;
        $this->routes[] = array($method, $route, $handler);
    }
}