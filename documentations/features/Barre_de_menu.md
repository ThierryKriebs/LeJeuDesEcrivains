
# Barre de menu
Bootstrap permet de créer des barres de menu responsives.
- Sur un téléphone ou en mode fenêtré (fenêtre de petite taille), elles prendront automatiquement la forme d'un menu burger. 
- Sur un ordinateur en plein écran, elles apparaîtront comme un menu classique.
- Le code de cette barre de menu se trouve dans le fichier Twig: `\templates\partials\nav.html.twig`. Ce fichier twig est appelé par: `\templates\base.html.twig`

Sources:
- [Site Officiel](https://getbootstrap.com/docs/4.0/components/navbar/) (en anglais) Bootstrap 4.0
- [Site Officiel explication sur la class navbar-expand{-sm|-md|-lg|-xl}](https://getbootstrap.com/docs/4.0/components/navbar/#containers)



## Code d'exemple:
Le code ci-dessous permet de générer facilement une barre de menu responsive.\
Pour l'utiliser, il suffit simplement de remplacer les 3 routes suivantes par des routes existantes dans l'application.
- `href="{{ path('app_lien_exemple1') }}`\
- `href="{{ path('app_lien_exemple1') }}`\
- `href="{{ path('admin') }}`\

```
<nav class="navbar navbar-expand-lg navbar-light  bg-dark" data-bs-theme="dark">
    <a class="navbar-brand" href="{{path('app_home')}}">
        <span class="navbar-brand my-2 my-lg-0 h1">{{ 'TITRE DE L\'APPLICATION' | trans }}<BR></span>
        <img class="img-fluid" width="200" src="{{ asset('images/logos/logo1.png') }}" alt="NOM DU LOGO">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarToggler">
        <ul class="navbar-nav me-auto">
            {% if is_granted('ROLE_ADMIN') %}
                <li class="nav-item">
                    <a class="nav-link text-white {{ app.current_route starts with 'admin' ? 'active' : '' }}" href="{{ path('admin') }}"> Administration</a>
                </li>
            
            {% endif %}
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ path('app_lien_exemple1') }}">{{ 'Nom du lien pour exemple' | trans }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="{{ path('app_lien_exemple2') }}">{{ 'Nom du lien pour exemple2' | trans }}</a>
                </li>
        </ul>
    </div>
</nav>
```


## Explications:
- La navbar est-une navbar classique de Bootstrap. Une barre de navigation se déclare avec la balise: `<nav> </nav>`
- Toutes les navbar de bootstrap ont pour class `navbar`. 
- La navbar doit également avoir la class `navbar-expand-lg`. C'est elle qui permet, en cas de changement de taille de la fenêtre, de switcher automatiquement d'un affichage classique à un affichage Burger. Pour cette class, plusieurs options sont en réalité possibles:  `.navbar-expand{-sm|-md|-lg|-xl}`. Elles influent sur la taille minimum du menu classique, en-dessous de laquelle celui-ci se transformera en menu burger (voir documentation officielle pour plus de détails).
- Le contenu de la navbar (les différents liens), se trouvent dans une div à l'intérieur de la balise `<nav>` Cette div doit notamment avoir la class `collapse`, afin de regrouper les liens dans le menu Burger.
- A cette navbar on ajoute un bouton `navbar-toggler` qui permettra sur un petit affichage (ex: smartphone), de faire apparaître et disparaître les liens dans le menu burger. Pour cela, ce bouton doit notamment avoir une propriété ` data-target` qui indique l'id de la div contenant les différents liens du menu. Ici: ` data-target="#navbarToggler"`
