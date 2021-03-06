<?php

namespace MadWeb\Seoable\Test;

use BadMethodCallException;

class SeoableMetaTest extends TestCase
{
    protected $seoMeta;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seoMeta = $this->app->make('seotools.metatags');
    }

    /** @test */
    public function filling_meta_tags()
    {
        $this->setUpModel();

        $title = $this->app['translator']->get(
            'seo.'.\MadWeb\Seoable\Test\Models\Post::class.'.title',
            ['title' => $this->testPost->title]
        );

        $description = $this->app['translator']->get(
            'seo.'.\MadWeb\Seoable\Test\Models\Post::class.'.description',
            ['description' => $this->testPost->description]
        );

        $keywords = implode(', ', $this->testPost->keywords);

        $fullTitle = "$title - It's Over 9000!";

        $fullHeader = '<title>'.$fullTitle.'</title>';
        $fullHeader .= "<meta name=\"description\" content=\"$description\">";
        $fullHeader .= "<meta name=\"keywords\" content=\"$keywords\">";
        $fullHeader .= "<meta name=\"foo\" content=\"{$this->testPost->title}\">";
        $fullHeader .= "<meta name=\"some\" content=\"{$this->testPost->title}\">";
        $fullHeader .= "<meta name=\"new\" content=\"{$this->testPost->title}\">";
        $fullHeader .= "<link rel=\"canonical\" href=\"{$this->testPost->canonical}\">";
        $fullHeader .= "<link rel=\"prev\" href=\"{$this->testPost->prev}\">";
        $fullHeader .= "<link rel=\"next\" href=\"{$this->testPost->next}\">";
        $fullHeader .= "<link rel=\"alternate\" hreflang=\"ru\" href=\"{$this->testPost->lang}\">";
        $fullHeader .= "<link rel=\"alternate\" hreflang=\"en\" href=\"{$this->testPost->lang}\">";

        $this->setRightAssertion($fullHeader);
    }

    /** @test */
    public function raw_properties()
    {
        $title = 'Some awesome title';

        $description = 'Some awesome description';
        $this->testPost->seoable()
            ->setTitleRaw($title)
            ->setDescriptionRaw($description);

        $expectedMeta = '<title>'.$title.' - It\'s Over 9000!</title>';
        $expectedMeta .= "<meta name=\"description\" content=\"$description\">";

        $this->setRightAssertion($expectedMeta);
    }

    /** @test */
    public function invalid_meta_method()
    {
        $this->expectException(BadMethodCallException::class);

        $this->testPost->seoable()
            ->setSomething();
    }

    protected function generatedTags()
    {
        return $this->seoMeta->generate(true);
    }

    protected function setUpModel()
    {
        $this->testPost->seoable()
            ->setTitle('title')
            ->setDescription('description')
            ->setCanonical('canonical')
            ->setPrev('prev')
            ->setNext('next')
            ->setKeywords('keywords')
            ->setLanguages([
                [
                    'lang' => 'ru',
                    'url' => 'lang',
                ],
            ])
            ->addLanguage('en', 'lang')
            ->addMeta('foo', 'title')
            ->setMeta([
                [
                    'meta' => 'some',
                    'value' => 'title',
                ],
                [
                    'meta' => 'new',
                    'value' => 'title',
                ],
            ]);
    }
}
