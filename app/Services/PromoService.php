<?php
namespace App\Services;
use App\Repositories\PromoRepository;
use App\Repositories\ReferentielRepository;

class PromoService
{
    protected $promoRepository;
    protected $referentielRepository; // Ajoutez cette ligne pour le référentiel

    public function __construct(PromoRepository $promoRepository, ReferentielRepository $referentielRepository)
    {
        $this->promoRepository = $promoRepository;
        $this->referentielRepository = $referentielRepository;
    }

    public function getAllPromos()
    {
        return $this->promoRepository->getPromos();
    }

    public function createPromo($data)
    {
        // Vérification du libellé unique
        if (!$this->promoRepository->checkUniqueLibelle($data['libelle'])) {
            return response()->json(['message' => 'Le libellé existe déjà.'], 422);
        }

        // Calcul de la durée si la date_fin est fournie
        if (isset($data['date_debut']) && isset($data['date_fin'])) {
            $dateDebut = new \DateTime($data['date_debut']);
            $dateFin = new \DateTime($data['date_fin']);
            $duree = $dateDebut->diff($dateFin)->m;
            $data['duree'] = $duree;
        }

        // Calcul de la date de fin si la durée est fournie
        if (isset($data['date_debut']) && isset($data['duree'])) {
            $dateDebut = new \DateTime($data['date_debut']);
            $dateFin = $dateDebut->add(new \DateInterval('P' . $data['duree'] . 'M'));
            $data['date_fin'] = $dateFin->format('Y-m-d');
        }

        // Par défaut, l'état de la promotion est "Inactif"
        $data['etat'] = 'Inactif';

        // Enregistrement dans Firebase
        return $this->promoRepository->create($data);
    }

    // Méthode pour ajouter un référentiel à une promotion
    // DEBUGGING:
    // Ajouter un référentiel à une promo
    public function addReferentielToPromo($promoUid, $referentielUid)
    {
        // Récupère le référentiel par son UID
        $referentiel = $this->promoRepository->getReferentielByUid($referentielUid);

        if (!$referentiel) {
            return response()->json(['message' => 'Referentiel not found'], 404);
        }

        // Vérifier si le référentiel est actif
        if (isset($referentiel['StatutReferentiel']) && $referentiel['StatutReferentiel'] === "Inactif") {
            return response()->json(['message' => 'Ce référentiel est inactif'], 400);
        }

        // Ajoute le référentiel à la promo
        $result = $this->promoRepository->addReferentielToPromo($promoUid, $referentielUid);

        if ($result) {
            return response()->json(['message' => 'Referentiel added to promo successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to add referentiel to promo'], 500);
        }
    }


    public function updatePromoEtat($promoId, $newEtat)
    {
        // Récupérer la promo à modifier
        $promo = $this->promoRepository->getPromoById($promoId);
        if (!$promo) {
            throw new \Exception("La promo n'existe pas.");
        }

        // Si la promo doit être activée, vérifiez les autres conditions
        if ($newEtat === 'Actif') {
            // Vérifiez s'il existe déjà une promo active
            $activePromo = $this->promoRepository->getActivePromo();
            if ($activePromo && $activePromo['id'] !== $promoId) {
                throw new \Exception("Une seule promo peut être active.");
            }

            // Vérifiez si la promo a un référentiel
            if (!$this->promoRepository->hasReferentiel($promoId)) {
                throw new \Exception("Une promo sans référentiel ne peut pas être active.");
            }
        }

        // Mettre à jour l'état de la promo
        $this->promoRepository->updatePromoEtat($promoId, $newEtat);

        return "L'état de la promo a été mis à jour avec succès.";
    }

    //--------------------------------------------------------------
    public function getActivePromo()
    {
        $promo = $this->promoRepository->getActivePromo();

        if (!$promo) {
            throw new \Exception("Aucune promo active trouvée.");
        }

        return $promo;
    }

    //--------------------------------------------------------------

    // Service pour obtenir les référentiels d'une promo spécifique
    public function getReferentielsByPromo(string $promoId)
    {
        return $this->promoRepository->getReferentielsByPromo($promoId);
    }



}
