<?php
namespace Test\Kbize\Config;

use Kbize\Config\FilesystemConfigRepository;

class FilesystemConfigRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->parser = $this->getMock('Symfony\Component\Yaml\Parser');
        $this->dumper = $this->getMock('Symfony\Component\Yaml\Dumper');

        $this->repository = new FilesystemConfigRepository(
            '/tmp/'.str_replace('/','_',__DIR__.__file__),
            $this->parser,
            $this->dumper
        );
    }

    public function testToArrayReturnsAnEmptyArrayByDefault()
    {
        $this->assertEquals([], $this->repository->toArray());
    }

    public function testReturnsUpdatedDataAfterReplaceMethodIsCalled()
    {
        $data = [
            'foo' => 'bar',
        ];

        $this->repository->replace($data);
        $this->assertEquals($data, $this->repository->toArray());
    }

    public function testStoreDataOnFilesystemOnStore()
    {
        $data = [
            'foo' => 'bar',
        ];
        $this->repository->replace($data);

        $this->dumper->expects($this->once())
            ->method('dump')
            ->with($data)
        ;

        //TODO:! extract class to check file_put_contents
        //...

        $this->repository->store();
    }

    public function testFilePathContentIsUsedToInitConfigData()
    {
        $data = [
            'foo' => 'bar',
        ];

        $this->parser->expects($this->once())
            ->method('parse')
            ->will($this->returnValue($data))
        ;

        //TODO:! extract class to check file_get_contents
        touch('/tmp/'.str_replace('/','_',__DIR__.__file__));
        $this->repository = new FilesystemConfigRepository(
            '/tmp/'.str_replace('/','_',__DIR__.__file__),
            $this->parser,
            $this->dumper
        );

        $this->assertEquals($data, $this->repository->toArray());
    }
}
