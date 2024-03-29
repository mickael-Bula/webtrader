# ALGORITHME DE WEBTRADER

Une position est un ordre d'achat suivi d'un ordre de vente.

## 1 - Chaque jour après 18:00

Si un nouveau plus haut est réalisé :
```php
LAST_HIGH.higher = CAC.higher;
LAST_HIGH.buyLimit = LAST_HIGH.higher - 10%;
```

## 2 - On récupère toutes les POSITION.isWaiting

Cette récupération se fait sur la base des clés étrangères :
```php
$buyLimit = $cacRepository->findOneBy([], ["id" => "DESC"]);
findBy(["buyLimit" => $buyLimit, "isWaiting" => "true"]);
```
Ou bien peut-être...
```php
$lastHigh = new LastHigh();
$lastHigh = ($lastHigh->getHigher())[-1];   // pour récupérer le dernier indice du tableau
```
NOTE : ces méthodes sont à vérifier.

Ensuite, on boucle sur le tableau.
Si une position a une buyTarget inférieure à Cac.lower, alors toutes les positions récupérées précédemment reçoivent :

```php
POSITION.isRunning = true;
POSITION.isWaiting = false;
```
En outre, celles ayant une buyTarget inférieure à Cac.lower reçoivent :
```php
isActive = true;
```
Pour celles-ci, on crée immédiatement un nouvel ordre, cette fois à la vente.
L'objectif de revente est :
```php
sellTarget = buyTarget + 10%;
```
On met à jour les propriétés LAST_HIGH.higher et LAST_HIGH.buyLimit :
```php
LAST_HIGH.higher = CAC.higher
LAST_HIGH.buyLimit = LAST_HIGH.higher - 10%
```
Enfin, on renseigne la propriété buyPrice :
```php
buyPrice = buyTarget
```
NOTE : cette propriété va nous permettre de renseigner le véritable prix d'achat, lequel peut avoir été exécuté à un cours différent de celui fixé (cas d'un gap de cotation par exemple).

## 3 - On récupère toutes les POSITION.isRunning

Si parmi celles-ci il y a une POSITION.isActive qui a une sellTarget inférieure à Cac.higher, la position devient :
```php
isActive = false;
isRunning = false;
isClosed = true;
saleDate = new DateTime(now);   // date du jour
salePrice = sellTarget;         // qui pourra être ajustée à l'image de buySale
```

## Algorithme de vérification des positions qui passent au statut isRunning

Nous sommes à l'étape de vérification des données scrappées. On boucle sur ces données.
A chaque tour de boucle, on récupère toutes les positions qui ont le statut isWaiting, triées par buyTarget.
On vérifie si row.lower < position.isWaiting.buyTarget.
Si c'est le cas, on change le statut de la position a isRunning et on lui fournit les données nécessaires.
NOTE : on renseigne la propriété informative buyPrice qui correspond au véritable prix d'achat (le cours d'exécution réel)
On vérifie s'il existe des position.isWaiting qui ont un lastHigh inférieur à cette position (on compare les ids).
Si c'est le cas, on les supprime pour ne garder qu'une seule série active de trades.
Si c'est la première position d'une nouvelle série de 3 ordres, on crée les nouveaux lastHigh, buyLimit et 3 positions isWaiting.

Pour ce même tour de boucle, on passe ensuite à la vérification des positions isRunning.
n récupère toutes les positions qui ont le statut isRunning, triées par sellTarget.
On vérifie si row.higher > position.isRunning.sellTarget.
Si c'est le cas, on change le statut de la position a isClosed et on lui fournit les données nécessaires.

## Algorithme de mise à jour des données d'un utilisateur

Il faut dissocier les mises à jour des données du cac et du Lvc de celles de l'utilisateur.
En effet, les données boursières étatn mises à jour une fois pour l'ensemble des utilisateurs, les données propres à un utilisateur doivent être vérifiées indépendamment de celles du Cac.

Pour garder une référence à la dernière visite d'un utilisateur, on gardera donc un lien vers l'id du dernier Cac présent en base lors de cette visite (la dernière entrée du Cac dans le cas de la première visite).

En comparant la référence du dernier Cac enregistré pour l'utilisateur et le dernier Cac en base, on pourra boucler sur toutes ces dates, de la plus ancienne à la plus récente.

