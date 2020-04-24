<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Renderer\Block;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Block\IndentedCode;
use League\CommonMark\Extension\CommonMark\Renderer\Block\IndentedCodeRenderer;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Parser\Context;
use League\CommonMark\Tests\Unit\Renderer\FakeHtmlRenderer;
use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

class IndentedCodeRendererTest extends TestCase
{
    /**
     * @var IndentedCodeRenderer
     */
    protected $renderer;

    protected function setUp(): void
    {
        $this->renderer = new IndentedCodeRenderer();
    }

    public function testRender()
    {
        $document = new Document();
        $context = new Context($document, new Environment());

        $block = new IndentedCode();
        $block->data['attributes'] = ['id' => 'foo'];
        $block->addLine('echo "hello world!";');

        $document->appendChild($block);
        $block->finalize($context, 1);

        $fakeRenderer = new FakeHtmlRenderer();

        $result = $this->renderer->render($block, $fakeRenderer);

        $this->assertTrue($result instanceof HtmlElement);
        $this->assertEquals('pre', $result->getTagName());

        $code = $result->getContents(false);
        $this->assertTrue($code instanceof HtmlElement);
        $this->assertEquals('code', $code->getTagName());
        $this->assertNull($code->getAttribute('class'));
        $this->assertEquals(['id' => 'foo'], $code->getAllAttributes());
        $this->assertStringContainsString('hello world', $code->getContents(true));
    }

    public function testRenderWithInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        $inline = $this->getMockForAbstractClass(AbstractBlock::class);
        $fakeRenderer = new FakeHtmlRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }
}