<?php

/* overall/topnav.twig */
class __TwigTemplate_1895d29d3fa49354691ccbc1a7149745c36459084fa1f5a906c09bf327002080 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<header class=\"site-header\">

  <!-- Top navbar & branding -->
  <nav class=\"navbar navbar-default\">
    <div class=\"container\">

      <!-- Toggle buttons and brand -->
      <div class=\"navbar-header\">
        <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#navbar\" aria-expanded=\"true\" aria-controls=\"navbar\">
          <span class=\"glyphicon glyphicon-option-vertical\"></span>
        </button>

        <button type=\"button\" class=\"navbar-toggle for-sidebar\" data-toggle=\"offcanvas\">
          <span class=\"icon-bar\"></span>
          <span class=\"icon-bar\"></span>
          <span class=\"icon-bar\"></span>
        </button>

        <a class=\"navbar-brand\" href=\"home/\"><img src=\"views/assets/img/logo.png\" alt=\"Ocrend Framework\"></a>
      </div>
      <!-- END Toggle buttons and brand -->

      <!-- Top navbar -->
      <div id=\"navbar\" class=\"navbar-collapse collapse\" aria-expanded=\"true\" role=\"banner\">
        <ul class=\"nav navbar-nav navbar-right\">
          <li class=\"active\"><a href=\"sobre/\">Documentación</a></li>
          <li><a href=\"https://github.com/prinick96/Ocrend-Framework/\" target=\"_blank\"><i class=\"fa fa-github\"></i> Github</a></li>
          <li class=\"hero\"><a href=\"https://github.com/prinick96/Ocrend-Framework/releases\" target=\"_blank\"><i class=\"fa fa-download\"></i> Descargar</a></li>
        </ul>
      </div>
      <!-- END Top navbar -->

    </div>
  </nav>
  <!-- END Top navbar & branding -->

</header>
";
    }

    public function getTemplateName()
    {
        return "overall/topnav.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "overall/topnav.twig", "C:\\xampp\\htdocs\\Framework-Doc\\templates\\twig\\overall\\topnav.twig");
    }
}
