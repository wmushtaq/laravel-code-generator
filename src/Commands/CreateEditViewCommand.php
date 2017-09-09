<?php

namespace CrestApps\CodeGenerator\Commands;

use CrestApps\CodeGenerator\Support\ResourceTransformer;
use CrestApps\CodeGenerator\Support\ViewsCommand;

class CreateEditViewCommand extends ViewsCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:edit-view
                            {model-name : The model name that this view will represent.}
                            {--resource-file= : The name of the resource-file to import from.}
                            {--views-directory= : The name of the directory to create the views under.}
                            {--routes-prefix= : The routes prefix.}
                            {--language-filename= : The name of the language file.}
                            {--layout-name=layouts.app : This will extract the validation into a request form class.}
                            {--template-name= : The template name to use when generating the code.}
                            {--force : This option will override the view if one already exists.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an edit-views for the model.';

    /**
     * Gets the name of the stub to process.
     *
     * @return string
     */
    protected function getStubName()
    {
        return 'edit.blade';
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    protected function handleCreateView()
    {
        $input = $this->getCommandInput();
        $resources = ResourceTransformer::fromFile($input->resourceFile, $input->languageFileName);
        $destenationFile = $this->getDestinationViewFullname($input->viewsDirectory, $input->prefix, 'edit');

        if ($this->canCreateView($destenationFile, $input->force, $resources->fields)) {
            $stub = $this->getStub();

            $this->createLanguageFile($input->languageFileName, $input->resourceFile, $input->modelName)
                ->createMissingViews($input)
                ->replaceCommonTemplates($stub, $input, $resources->fields)
                ->replaceFileUpload($stub, $resources->fields)
                ->replacePrimaryKey($stub, $this->getPrimaryKeyName($resources->fields))
                ->replaceModelHeader($stub, $this->getHeaderFieldAccessor($resources->fields, $input->modelName))
                ->createFile($destenationFile, $stub)
                ->info('Edit view was crafted successfully.');
        }
    }
}
