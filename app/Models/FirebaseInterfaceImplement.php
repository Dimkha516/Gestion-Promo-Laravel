<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class FirebaseInterfaceImplement implements FirebaseInterface
{
    protected $firebase;

    public function __construct()
    {
        $credentials = base64_decode(env('FIREBASE_CREDENTIALS'));
        $tempFilePath = storage_path('app/firebase/temp_credentials.json');
        file_put_contents($tempFilePath, $credentials);


        // dd(base64_decode( config('services.firebase.credentials')));
        $factory = (new Factory)
            // ->withServiceAccount(storage_path(config('services.firebase.credentials')))
            ->withServiceAccount($tempFilePath)
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
        $this->firebase = $factory->createDatabase();
    }

    public function uploadPhoto($file)
    {
        $storage = (new Factory)->withServiceAccount(config('services.firebase.credentials'))->createStorage();
        $bucket = $storage->getBucket();

        // Créer un nom de fichier unique
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        $bucket->upload(
            fopen($file->getPathname(), 'r'),
            [
                'name' => 'photos/' . $fileName
            ]
        );

        // Retourner l'URL de l'image
        return 'https://storage.googleapis.com/' . $bucket->info()['name'] . '/photos/' . $fileName;
    }


    // INSERTION QUI SERA APPELLÉE POUR LA COLLECTION REFERENTIELS
    public function createInFirebase(array $data)
    {
        if (isset($data['photo'])) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->firebase->getReference('referentiels')->push($data);

    }

    public function getFirebaseUid($referentiel)
    {
        try {
            // Récupérer l'entièreté des référentiels depuis Firebase
            $referentielSnapshot = $this->firebase->getReference('referentiels')->getSnapshot();

            if ($referentielSnapshot->exists()) {
                // Parcourir tous les référentiels pour trouver celui correspondant
                foreach ($referentielSnapshot->getValue() as $uid => $referentielData) {
                    // Comparer une propriété unique du référentiel (ici libelleReferentiel) pour récupérer l'UID
                    if ($referentielData['libelleReferentiel'] === $referentiel['libelleReferentiel']) {
                        return $uid; // Retourner l'UID correspondant
                    }
                }
            }


        } catch (\Exception $e) {
            // Loguer l'exception en cas de problème
            \Log::error('Error fetching UID from Firebase:', ['exception' => $e->getMessage()]);
        }

        return null; // UID non trouvé
    }

    public function getFirebaseUid2($libelleReferentiel)
    {
        try {
            // Récupérer l'entièreté des référentiels depuis Firebase
            $referentielSnapshot = $this->firebase->getReference('referentiels')->getSnapshot();

            // Vérifier l'existence des référentiels
            if ($referentielSnapshot->exists()) {
                foreach ($referentielSnapshot->getValue() as $uid => $referentielData) {
                    // Comparer une propriété unique du référentiel (ici libelleReferentiel) pour récupérer l'UID
                    if (isset($referentielData['libelleReferentiel']) && $referentielData['libelleReferentiel'] === $libelleReferentiel) {
                        return $uid; // Retourner l'UID correspondant
                    }
                }
            }
        } catch (\Exception $e) {
            // Loguer l'exception en cas de problème
            \Log::error('Erreur lors de la récupération de l\'UID depuis Firebase:', ['exception' => $e->getMessage()]);
        }

        return null; // UID non trouvé
    }


    // Mettre à jour une ressource dans Firebase
    public function updateInFirebase($id, array $data)
    {
        try {
            // Mettez à jour le document dans Firebase
            $this->firebase->getReference('referentiels/' . $id)->update($data);

            return true;
        } catch (\Exception $e) {
            // Log the exception
            \Log::error('Error updating in Firebase:', ['id' => $id, 'data' => $data, 'exception' => $e->getMessage()]);

            return false;
        }

    }

    // Supprimer une ressource de Firebase
    public function deleteFromFirebase($id)
    {
        return $this->firebase->getReference('referentiels/' . $id)->remove();
    }

    // Trouver une ressource dans Firebase par ID
    public function findInFirebase($id)
    {
        return $this->firebase->getReference('referentiels/' . $id)->getValue();

    }

    // Récupérer toutes les ressources d'une collection
    public function getAllFromFirebase($path)
    {
        $snapshot = $this->firebase->getReference($path)->getSnapshot();
        $value = $snapshot->getValue();

        // Retourne un tableau vide si aucun enregistrement n'est trouvé
        return is_array($value) ? $value : [];

        // return $this->firebase->getReference($path)->getValue();
    }


    // INSERTION QUI SERA APPELLÉE POUR LA COLLECTION PROMO
    public function insertIntoFirebase($collection, array $data)
    {
        if (isset($data['photo'])) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        try {
            $reference = $this->firebase->getReference($collection)->push($data);
            return $reference->getKey(); // Retourne l'ID généré dans Firebase
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'insertion dans Firebase: ' . $e->getMessage());
            return false;
        }
    }


    public function getReference($path)
    {
        return $this->firebase->getReference($path);
    }
}

