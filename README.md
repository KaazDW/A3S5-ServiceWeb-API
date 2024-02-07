### Fonctionnalités List :

- ✅ En tant qu’utilisateur non connecté, je peux consulter la liste des magasins près de chez moi
    `/magasins/all & /magasins/near dans MagasinController`
- ✅ En tant qu’utilisateur non connecté, je peux consulter les produits vendus dans ces magasins
    `/produits/all dans ProduitController`
- En tant qu'utilisateur non connecté, je peux consulter si un produit est disponible en stock
- En tant qu’utilisateur non connecté, je peux écrire un message à un vendeur
- En tant qu'utilisateur non connecté, je peux m'inscrire afin de devenir un client
- En tant que client, je peux commander un ou plusieurs produits dans un magasin
- En tant que client, je dispose d'une liste de créneau disponible pour récupérer ma commande
- En tant que client, je souhaite pouvoir réserver un créneau pour récupérer ma commande.
- En tant qu’administrateur, je peux déclarer de nouvelles boutiques et mettre à jour le stock en ajoutant ou retirant des articles
- En tant qu’administrateur, je peux notifier par mail mes clients que leur commande est validée.

### Rooting List :
```
/magasins/all
/magasins/near

/produits/all
```

### Entity List :
- Magasin
- Produit
- ...