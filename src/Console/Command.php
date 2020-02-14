<?php

namespace Laravel\Envoy\Console;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

trait Command
{
    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        return (int) $this->fire();
    }

    /**
     * Get an argument from the input.
     *
     * @param  string  $key
     * @return string
     */
    public function argument($key)
    {
        return $this->input->getArgument($key);
    }

    /**
     * Get an option from the input.
     *
     * @param  string  $key
     * @return string
     */
    public function option($key)
    {
        return $this->input->getOption($key);
    }

    /**
     * Ask the user the given question.
     *
     * @param  string  $question
     * @return string
     */
    public function ask($question)
    {
        $question = '<comment>'.$question.'</comment> ';

        $question = new Question($question);

        return $this->getHelperSet()->get('question')->ask($this->input, $this->output, $question);
    }

    /**
     * Confirm the operation with the user.
     *
     * @param  string  $task
     * @param  string  $question
     * @return bool
     */
    public function confirmTaskWithUser($task, $question)
    {
        $question = $question === true ? 'Are you sure you want to run the ['.$task.'] task?' : (string) $question;

        $question = '<comment>'.$question.' [y/N]:</comment> ';

        $question = new ConfirmationQuestion($question, false);

        return $this->getHelperSet()->get('question')->ask($this->input, $this->output, $question);
    }

    /**
     * Ask the user the given secret question.
     *
     * @param  string  $question
     * @return string
     */
    public function secret($question)
    {
        $question = '<comment>'.$question.'</comment> ';

        $question = new Question($question);
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        return $this->getHelperSet()->get('question')->ask($this->input, $this->output, $question);
    }

    /**
     * Gather the dynamic options for the command.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [];

        // Here we will gather all of the command line options that have been specified with
        // the double hyphens in front of their name. We will make these available to the
        // Blade task file so they can be used in echo statements and other structures.
        foreach ($_SERVER['argv'] as $argument) {
            if (! Str::startsWith($argument, '--') || in_array($argument, $this->ignoreOptions)) {
                continue;
            }

            $option = explode('=', substr($argument, 2), 2);

            if (count($option) == 1) {
                $option[1] = true;
            }

            $options[$option[0]] = $option[1];
        }

        return $options;
    }
}
