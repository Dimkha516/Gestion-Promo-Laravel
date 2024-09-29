<?php
namespace App\Repositories;
use App\Models\FirebaseInterfaceImplement;

class PromoRepository
{
    protected $firebase;

    public function __construct(FirebaseInterfaceImplement $firebase)
    {
        $this->firebase = $firebase;
    }


    // LISTER TOUTES LES PROMOS: 
    public function getPromos()
    {
        $promos = $this->firebase->getAllFromFirebase('promos');

        return $promos;

    }

    // Vérification si le libellé est unique dans Firebase
    public function checkUniqueLibelle($libelle)
    {
        $promos = $this->firebase->getAllFromFirebase('promos');
        foreach ($promos as $promo) {
            if (isset($promo['libelle']) && $promo['libelle'] === $libelle) {
                return false; // Le libellé existe déjà
            }
        }
        return true;
    }

    // Création d'une promotion
    public function create($data)
    {
        return $this->firebase->insertIntoFirebase('promos', $data);
    }



    // Trouver une promotion par ID
    public function findPromo($promoId)
    {
        $promos = $this->firebase->getAllFromFirebase('promos');
        foreach ($promos as $promo) {
            if ($promo['uid'] === $promoId) { // Assurez-vous que la clé utilisée ici est correcte
                return $promo;
            }
        }
        return null;
    }

    // Mettre à jour les référentiels d'une promotion
    public function updateReferentiels($promoId, array $data)
    {
        return $this->firebase->updateInFirebase($promoId, $data);
    }

    public function getReferentielUidByLibelle($libelleReferentiel)
    {
        // \Log::info('Recherche du référentiel avec le libellé : ' . $libelleReferentiel);
        $uid = $this->firebase->getFirebaseUid2($libelleReferentiel);
        if ($uid) {
            // \Log::info('UID trouvé : ' . $uid);
        } else {
            // \Log::info('Référentiel non trouvé pour le libellé : ' . $libelleReferentiel);
        }

        return $uid;
    }

    // DEBUGGING:
    // Récupère un référentiel en fonction de son UID
    public function getReferentielByUid($uid)
    {
        try {
            // Récupère tous les référentiels depuis Firebase
            $referentiels = $this->firebase->getReference('referentiels')->getValue();

            // Parcourt tous les référentiels pour trouver celui correspondant à l'UID
            if ($referentiels) {
                foreach ($referentiels as $key => $referentiel) {
                    if ($key === $uid) {
                        return $referentiel;
                    }
                }
            }

            return null; // Référentiel non trouvé
        } catch (\Exception $e) {
            \Log::error('Error fetching referentiel from Firebase:', ['exception' => $e->getMessage()]);
            return null;
        }
    }


    public function addReferentielToPromo($promoUid, $referentielUid)
    {
        try {
            // Obtenir la référence de la promo dans Firebase
            $promoRef = $this->firebase->getReference('promos/' . $promoUid);

            // Récupérer les données actuelles de la promo
            $promo = $promoRef->getValue();  // Récupère un snapshot pour les données actuelles

            if (!$promo) {
                return false; // Promo non trouvée
            }

            // Vérifier si le tableau 'referentiels' existe déjà, sinon, l'initialiser
            if (!isset($promo['referentiels']) || !is_array($promo['referentiels'])) {
                $promo['referentiels'] = [];
            }

            // Ajouter le référentiel à la promo
            $promo['referentiels'][] = $referentielUid;

            // Mettre à jour les données dans Firebase
            $promoRef->update([
                'referentiels' => $promo['referentiels']
            ]);


            return true;
        } catch (\Exception $e) {
            \Log::error('Error adding referentiel to promo:', ['exception' => $e->getMessage()]);
            return false;
        }
    }


    //-----------------------CHANGER ETAT PROMO:
    public function getPromoById($promoId)
    {
        // Récupérer une promo par son ID dans Firebase
        return $this->firebase->getReference('promos/' . $promoId)->getValue();
    }


    public function getActivePromo()
    {
        // Récupérer la promo avec l'état 'Actif'
        $promos = $this->firebase->getReference('promos')->orderByChild('etat')->equalTo('Actif')->getValue();
        return !empty($promos) ? reset($promos) : null;
    }

    public function updatePromoEtat($promoId, $etat)
    {
        // Mise à jour de l'attribut 'etat' de la promo
        return $this->firebase->getReference('promos/' . $promoId)->update(['etat' => $etat]);
    }

    public function hasReferentiel($promoId)
    {
        // Vérifie si une promo a un référentiel associé
        $promo = $this->getPromoById($promoId);
        return isset($promo['referentiels']);
    }
    //----------------------------------------------------------------------
// Récupérer les référentiels d'une promo spécifique
    public function getReferentielsByPromo(string $promoId)
    {
        $promoSnapshot = $this->firebase->getReference('promos/' . $promoId)
            ->getSnapshot();

        if (!$promoSnapshot->exists()) {
            return null; // La promo n'existe pas
        }

        // Extraire les référentiels de la promo
        $promoData = $promoSnapshot->getValue();

        return isset($promoData['referentiels']) ? $promoData['referentiels'] : [];
    }

}
