<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class TaskFileService
{
    private string $taskDir;
    private Filesystem $fileSystem;

    public function __construct(string $projectDir)
    {
        $this->taskDir = $projectDir . '/public/tasks';
        $this->fileSystem = new Filesystem();

        // Create the directory if it does not exist
        if (!$this->fileSystem->exists($this->taskDir)) {
            $this->fileSystem->mkdir($this->taskDir);
        }
    }

    public function createTask(string $title, string $description): void
    {
        $id = uniqid('', true);
        $filePath = $this->taskDir . '/' . $id . '.txt';

        $content = sprintf("Titre : %s\nDescription : %s\n", $title, $description);

        $this->fileSystem->dumpFile($filePath, $content); // Correct: utilisez `$fileSystem`
    }

    public function updateTask(string $id, string $title, string $description): void
    {
        $filePath = $this->taskDir . '/' . $id . '.txt';

        if (!$this->fileSystem->exists($filePath)) { // Correct: utilisez `$fileSystem`
            throw new \RuntimeException("Le fichier de tâche avec l'ID $id n'existe pas.");
        }

        $content = sprintf("Titre : %s\nDescription : %s\n", $title, $description);
        $this->fileSystem->dumpFile($filePath, $content); // Correct: utilisez `$fileSystem`
    }

    public function listTasks(): array
    {
        $files = scandir($this->taskDir);
        $tasks = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                $id = pathinfo($file, PATHINFO_FILENAME);
                $content = file($this->taskDir . '/' . $file);

                $titleLine = $content[0] ?? '';
                $title = trim(str_replace('Titre : ', '', $titleLine));

                $tasks[] = [
                    'id' => $id,
                    'title' => $title,
                ];
            }
        }

        return $tasks;
    }

    public function getTask(string $id): array
    {
        $filePath = $this->taskDir . '/' . $id . '.txt';

        if (!$this->fileSystem->exists($filePath)) { // Correct: utilisez `$fileSystem`
            throw new \Exception("Le fichier de tâche avec l'ID $id n'existe pas.");
        }

        $content = file($filePath);
        $title = trim(str_replace('Titre : ', '', $content[0] ?? ''));
        $description = trim(str_replace('Description : ', '', $content[1] ?? ''));

        return [
            'id' => $id,
            'title' => $title,
            'description' => $description,
        ];
    }

    public function deleteTask(string $id): void
    {
        $filePath = $this->taskDir . '/' . $id . '.txt';

        if (!$this->fileSystem->exists($filePath)) { // Correct: utilisez `$fileSystem`
            throw new \RuntimeException("Le fichier de tâche avec l'ID $id n'existe pas.");
        }

        $this->fileSystem->remove($filePath); // Correct: utilisez `$fileSystem`
    }
}
