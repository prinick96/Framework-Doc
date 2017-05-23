<?php

/* overall/menu.twig */
class __TwigTemplate_cf0447b984f83d617c451e22d7d046939cfa8b5afb1bce48693f8381274c2e3e extends Twig_Template
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
        echo "<aside class=\"col-md-3 col-sm-3 sidebar\">

  <ul class=\"sidenav dropable sticky\">
    <li>
      <a ";
        // line 5
        echo ((twig_in_filter(($context["controller"] ?? null), array(0 => "sobre", 1 => "descarga", 2 => "home"))) ? ("class=\"active\"") : (""));
        echo " href=\"#\">Introducción</a>
      <ul>
        <li><a ";
        // line 7
        echo ((twig_in_filter(($context["controller"] ?? null), array(0 => "sobre", 1 => "home"))) ? ("class=\"active\"") : (""));
        echo " href=\"sobre/\">Sobre el framework</a></li>
        <li><a ";
        // line 8
        echo (((($context["controller"] ?? null) == "descarga")) ? ("class=\"active\"") : (""));
        echo " href=\"descarga/\">Descarga e Instalación</a></li>
      </ul>
    </li>

    <li><a ";
        // line 12
        echo (((($context["controller"] ?? null) == "controladores")) ? ("class=\"active\"") : (""));
        echo " href=\"controladores/\">Controladores</a></li>
    <li><a ";
        // line 13
        echo (((($context["controller"] ?? null) == "rutas")) ? ("class=\"active\"") : (""));
        echo " href=\"rutas/\">Rutas</a></li>
    <li>
      <a ";
        // line 15
        echo (((($context["controller"] ?? null) == "helpers")) ? ("class=\"active\"") : (""));
        echo " href=\"#\">Helpers</a>
      <ul>
        <li><a ";
        // line 17
        echo ((((($context["controller"] ?? null) == "helpers") && (($context["method"] ?? null) == null))) ? ("class=\"active\"") : (""));
        echo " href=\"helpers/\">Los Helpers</a></li>
        <li><a ";
        // line 18
        echo (((($context["method"] ?? null) == "arrays")) ? ("class=\"active\"") : (""));
        echo " href=\"helpers/arrays/\">Arrays</a></li>
        <li><a ";
        // line 19
        echo (((($context["method"] ?? null) == "files")) ? ("class=\"active\"") : (""));
        echo " href=\"helpers/files/\">Files</a></li>
        <li><a ";
        // line 20
        echo (((($context["method"] ?? null) == "strings")) ? ("class=\"active\"") : (""));
        echo " href=\"helpers/strings/\">Strings</a></li>
        <li><a ";
        // line 21
        echo (((($context["method"] ?? null) == "emails")) ? ("class=\"active\"") : (""));
        echo " href=\"helpers/emails/\">Emails</a></li>
        <li><a ";
        // line 22
        echo (((($context["method"] ?? null) == "bootstrap")) ? ("class=\"active\"") : (""));
        echo " href=\"helpers/bootstrap/\">Bootstrap</a></li>
        <li><a ";
        // line 23
        echo (((($context["method"] ?? null) == "paypal")) ? ("class=\"active\"") : (""));
        echo " href=\"helpers/paypal/\">Paypal</a></li>
      </ul>
    </li>
    <li><a ";
        // line 26
        echo (((($context["controller"] ?? null) == "modelos")) ? ("class=\"active\"") : (""));
        echo " href=\"modelos/\">Modelos</a></li>


    <li><a ";
        // line 29
        echo (((($context["controller"] ?? null) == "funciones")) ? ("class=\"active\"") : (""));
        echo " href=\"funciones/\">Funciones</a></li>
    <li><a ";
        // line 30
        echo (((($context["controller"] ?? null) == "sesiones")) ? ("class=\"active\"") : (""));
        echo " href=\"sesiones/\">Sesiones</a></li>
    <li><a ";
        // line 31
        echo (((($context["controller"] ?? null) == "phpfirewall")) ? ("class=\"active\"") : (""));
        echo " href=\"phpfirewall/\">Firewall</a></li>
    <li><a ";
        // line 32
        echo (((($context["controller"] ?? null) == "debugger")) ? ("class=\"active\"") : (""));
        echo " href=\"debugger/\">Debug</a></li>
    <li><a ";
        // line 33
        echo (((($context["controller"] ?? null) == "generador")) ? ("class=\"active\"") : (""));
        echo " href=\"generador/\">Generador de código</a></li>

    <li>
      <a ";
        // line 36
        echo (((($context["controller"] ?? null) == "vistas")) ? ("class=\"active\"") : (""));
        echo " href=\"#\">Vistas</a>
      <ul>
        <li><a ";
        // line 38
        echo ((((($context["controller"] ?? null) == "vistas") && (($context["method"] ?? null) == null))) ? ("class=\"active\"") : (""));
        echo " href=\"vistas/\">Vistas</a></li>
        <li><a ";
        // line 39
        echo (((($context["method"] ?? null) == "plates")) ? ("class=\"active\"") : (""));
        echo " href=\"vistas/plates/\">PlatesPHP</a></li>
        <li><a ";
        // line 40
        echo (((($context["method"] ?? null) == "twig")) ? ("class=\"active\"") : (""));
        echo " href=\"vistas/twig/\">Twig</a></li>
      </ul>
    </li>

    <li><a ";
        // line 44
        echo (((($context["controller"] ?? null) == "apirest")) ? ("class=\"active\"") : (""));
        echo " href=\"apirest/\">Api Rest</a></li>

  </ul>

</aside>
";
    }

    public function getTemplateName()
    {
        return "overall/menu.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  133 => 44,  126 => 40,  122 => 39,  118 => 38,  113 => 36,  107 => 33,  103 => 32,  99 => 31,  95 => 30,  91 => 29,  85 => 26,  79 => 23,  75 => 22,  71 => 21,  67 => 20,  63 => 19,  59 => 18,  55 => 17,  50 => 15,  45 => 13,  41 => 12,  34 => 8,  30 => 7,  25 => 5,  19 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "overall/menu.twig", "C:\\xampp\\htdocs\\Framework-Doc\\templates\\twig\\overall\\menu.twig");
    }
}
