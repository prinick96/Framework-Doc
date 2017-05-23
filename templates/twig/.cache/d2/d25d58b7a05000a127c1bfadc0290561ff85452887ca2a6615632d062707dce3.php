<?php

/* overall/header.twig */
class __TwigTemplate_ff051c9303830e54ce2a2eb841d3920abfa492a551c1aa51d753167408b3a94d extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'appBody' => array($this, 'block_appBody'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"es\">
  <head>
    <base href=\"";
        // line 4
        echo twig_escape_filter($this->env, twig_constant("URL"), "html", null, true);
        echo "\" />

    <meta charset=\"utf-8\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
    <meta name=\"description\" content=\"Documentación oficial de Ocrend Framework, Framework MVC Escrito en PHP 7 ideal para principiantes y expertos en desarrollo web con PHP y PDO.\">
    <meta name=\"keywords\" content=\"documentacion,framework,php 7,ocrend,curso,español,pdo,herramientas,programación\">
    <meta name=\"application-name\" content=\"";
        // line 11
        echo twig_escape_filter($this->env, twig_constant("APP"), "html", null, true);
        echo "\" />
    <meta name=\"author\" content=\"www.ocrend.com\" />

    <title>";
        // line 14
        echo twig_escape_filter($this->env, twig_constant("APP"), "html", null, true);
        echo "</title>

    <!-- Styles -->
    <link href=\"views/assets/css/theDocs.all.min.css\" rel=\"stylesheet\">
    <link href=\"views/assets/css/theDocs.css\" rel=\"stylesheet\">
    <link href=\"views/assets/css/custom.css\" rel=\"stylesheet\">
    <link href=\"views/assets/css/skin-ocrend.css\" rel=\"stylesheet\">

    <!-- Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Raleway:100,300,400,500%7CLato:300,400' rel='stylesheet' type='text/css'>

    <!-- Favicons -->
    <link type=\"image/x-icon\" href=\"views/favicon.ico\" rel=\"shortcut icon\" />
    <link href=\"views/app/images/apple-touch-icon.png\" rel=\"apple-touch-icon-precomposed\" />

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src=\"https://buttons.github.io/buttons.js\"></script>
  </head>
<body>
";
        // line 33
        $this->loadTemplate("overall/topnav", "overall/header.twig", 33)->display($context);
        // line 34
        $this->displayBlock('appBody', $context, $blocks);
        // line 36
        $this->loadTemplate("overall/footer", "overall/header.twig", 36)->display($context);
        // line 37
        echo "</body>
</html>";
    }

    // line 34
    public function block_appBody($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "overall/header.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  74 => 34,  69 => 37,  67 => 36,  65 => 34,  63 => 33,  41 => 14,  35 => 11,  25 => 4,  20 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "overall/header.twig", "C:\\xampp\\htdocs\\Framework-Doc\\templates\\twig\\overall\\header.twig");
    }
}
