<?php

namespace Parabol\BaseBundle\Component\Console;



class SymfonyStyle extends \Symfony\Component\Console\Style\SymfonyStyle {

	public function askUntilIncorrect($question, $default = null, $validator = null)
	{
		do
        {
            $answer = $this->ask($question, $default, $validator);
        }
        while($answer === false);

        return $answer;
	}

}