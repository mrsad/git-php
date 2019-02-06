<?php
use Tester\Assert;
use CzProject\GitPhp\GitRepository;
require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/exceptions.php';
require __DIR__ . '/../../src/GitRepository.php';

$cwd = getcwd();
chdir(TEMP_DIR);
$repo = GitRepository::cloneRepository('https://github.com/czproject/git-php.git');
chdir($cwd);

Assert::same(realpath(TEMP_DIR . '/git-php'), $repo->getRepositoryPath());

// repo is empty
Assert::false($repo->hasChanges());
$tags = $repo->getTags();
Assert::true(in_array('v1.0.0', $tags));
Assert::true(in_array('v1.0.1', $tags));
Assert::true(in_array('v2.0.0', $tags));

$branches = $repo->getBranches();
Assert::true(in_array('master', $branches));
Assert::true(in_array('remotes/origin/master', $branches));
Assert::true(in_array('remotes/origin/version-2', $branches));

Assert::same(array('master'), $repo->getLocalBranches());

// Specificky adresar
Tester\Helpers::purge(TEMP_DIR);
$repo = GitRepository::cloneRepository('https://github.com/czproject/git-php.git', TEMP_DIR . '/git-php2');

Assert::same(realpath(TEMP_DIR . '/git-php2'), $repo->getRepositoryPath());

// repo is empty
Assert::false($repo->hasChanges());
$tags = $repo->getTags();
Assert::true(in_array('v1.0.0', $tags));
Assert::true(in_array('v1.0.1', $tags));
Assert::true(in_array('v2.0.0', $tags));

$branches = $repo->getBranches();
Assert::true(in_array('master', $branches));
Assert::true(in_array('remotes/origin/master', $branches));
Assert::true(in_array('remotes/origin/version-2', $branches));

Assert::same(array('master'),$repo->getLocalBranches());

// error
$invalidRepoPath = TEMP_DIR . '/INVALID.git';
$invalidDest = TEMP_DIR . '/INVALID';
Assert::exception(function () use ($invalidRepoPath, $invalidDest) {
	GitRepository::cloneRepository($invalidRepoPath, $invalidDest);
}, 'CzProject\GitPhp\GitException', "Git clone failed (directory $invalidDest).\nfatal: repository '$invalidRepoPath' does not exist");
