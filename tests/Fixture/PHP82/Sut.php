<?php

declare(strict_types=1);

namespace Fixture\PHP82;

class Sut
{
	public function foo(A|(B&C) $arg) {
		var_dump($arg);
	}
}
