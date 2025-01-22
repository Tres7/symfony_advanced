<?php

namespace App\Command;

use App\Service\TaskFileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:task',
    description: 'Add a short description for your command',
)]
class TaskCommand extends Command
{
    protected static $defaultName = 'app:task';
    private TaskFileService $taskFileService;
    public function __construct(TaskFileService $taskFileService)
    {
        $this->taskFileService = $taskFileService;
        parent::__construct();
    }

//    protected function configure(): void
//    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
//    }

    /**
     * @return void
     * this function is used to configure the command
     * configure what arguments will be asked to the user
     */
//    protected function configure(): void
//    {
//        $this
//            ->setDescription('Gestion des tâches via la console')
//            ->addArgument('action', InputArgument::REQUIRED, 'Action à effectuer (create, list, get, update, delete)')
//            ->addArgument('id', InputArgument::OPTIONAL, 'ID de la tâche (nécessaire pour get, update, delete)')
//            ->addArgument('title', InputArgument::OPTIONAL, 'Titre de la tâche (nécessaire pour create, update)')
//            ->addArgument('description', InputArgument::OPTIONAL, 'Description de la tâche (nécessaire pour create, update)');
//    }
    protected function configure(): void
    {
        $this
            ->setDescription('Gestion des tâches via la console')
            ->addOption('action', null, InputOption::VALUE_REQUIRED, 'Action à effectuer (create, list, get, update, delete)')
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'ID de la tâche (nécessaire pour get, update, delete)')
            ->addOption('title', null, InputOption::VALUE_OPTIONAL, 'Titre de la tâche (nécessaire pour create, update)')
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, 'Description de la tâche (nécessaire pour create, update)');
    }


//    protected function execute(InputInterface $input, OutputInterface $output): int
//    {
//        $action = $input->getArgument('action');
//        $id = $input->getArgument('id');
//        $title = $input->getArgument('title');
//        $description = $input->getArgument('description');
//
//        switch ($action) {
//            case 'create':
//                $this->handleCreate($title, $description, $output);
//                break;
//
//            case 'list':
//                $this->handleList($output);
//                break;
//
//            case 'get':
//                $this->handleGet($id, $output);
//                break;
//
//            case 'update':
//                $this->handleUpdate($id, $title, $description, $output);
//                break;
//
//            case 'delete':
//                $this->handleDelete($id, $output);
//                break;
//
//            default:
//                $output->writeln('<error>Action inconnue. Les actions valides sont : create, list, get, update, delete.</error>');
//                return Command::FAILURE;
//        }
//
//        return Command::SUCCESS;
//    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Récupération des options
        $action = $input->getOption('action');
        $id = $input->getOption('id');
        $title = $input->getOption('title');
        $description = $input->getOption('description');

        // Vérification de l'action à effectuer
        switch ($action) {
            case 'create':
                $this->handleCreate($title, $description, $output);
                break;

            case 'list':
                $this->handleList($output);
                break;

            case 'get':
                $this->handleGet($id, $output);
                break;

            case 'update':
                $this->handleUpdate($id, $title, $description, $output);
                break;

            case 'delete':
                $this->handleDelete($id, $output);
                break;

            default:
                $output->writeln('<error>Action inconnue. Les actions valides sont : create, list, get, update, delete.</error>');
                return Command::FAILURE;
        }

        return Command::SUCCESS;
    }



    private function handleCreate(?string $title, ?string $description, OutputInterface $output): void
    {
        if ($title === null || $description === null) {
            $output->writeln('<error>Pour créer une tâche, vous devez fournir un titre et une description.</error>');
            return;
        }

        $this->taskFileService->createTask($title, $description);
        $output->writeln('<info>Tâche créée avec succès.</info>');
    }


    private function handleList(OutputInterface $output): void
    {
        $tasks = $this->taskFileService->listTasks();

        if (empty($tasks)) {
            $output->writeln('<info>Aucune tâche trouvée.</info>');
            return;
        }

        foreach ($tasks as $task) {
            $output->writeln(sprintf('<comment>ID:</comment> %s <info>Titre:</info> %s', $task['id'], $task['title']));
        }
    }

    private function handleGet(?string $id, OutputInterface $output): void
    {
        if (!$id) {
            $output->writeln('<error>Pour afficher une tâche, vous devez fournir un ID.</error>');
            return;
        }

        try {
            $task = $this->taskFileService->getTask($id);
            $output->writeln(sprintf('<info>ID:</info> %s', $task['id']));
            $output->writeln(sprintf('<info>Titre:</info> %s', $task['title']));
            $output->writeln(sprintf('<info>Description:</info> %s', $task['description']));
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }

    private function handleUpdate(?string $id, ?string $title, ?string $description, OutputInterface $output): void
    {
        if (!$id || !$title || !$description) {
            $output->writeln('<error>Pour modifier une tâche, vous devez fournir un ID, un titre et une description.</error>');
            return;
        }

        try {
            $this->taskFileService->updateTask($id, $title, $description);
            $output->writeln('<info>Tâche mise à jour avec succès.</info>');
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }

    private function handleDelete(?string $id, OutputInterface $output): void
    {
        if (!$id) {
            $output->writeln('<error>Pour supprimer une tâche, vous devez fournir un ID.</error>');
            return;
        }

        try {
            $this->taskFileService->deleteTask($id);
            $output->writeln('<info>Tâche supprimée avec succès.</info>');
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }


}
