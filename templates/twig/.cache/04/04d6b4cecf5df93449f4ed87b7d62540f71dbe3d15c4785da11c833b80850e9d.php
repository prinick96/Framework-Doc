<?php

/* content/content.twig */
class __TwigTemplate_c08344837c808683b7571d65214d6ddf87c55e8aaf69bf6b050e7ac6980c0b7c extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("overall/header", "content/content.twig", 1);
        $this->blocks = array(
            'appBody' => array($this, 'block_appBody'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "overall/header";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_appBody($context, array $blocks = array())
    {
        // line 3
        echo "    <main class=\"container\">
\t\t<div class=\"row\">
\t\t";
        // line 5
        $this->loadTemplate("overall/menu", "content/content.twig", 5)->display($context);
        // line 6
        echo "\t\t\t<article class=\"col-md-9 col-sm-9 main-content\" role=\"main\">
\t\t\t\t<header>
\t\t\t\t\t<p>";
        // line 8
        echo $this->env->getExtension('Parsedown')->text(($context["content"] ?? null));
        echo "</p>
\t\t\t\t</header>
\t\t\t</article>
\t\t</div>
\t</main>
";
    }

    public function getTemplateName()
    {
        return "content/content.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 8,  37 => 6,  35 => 5,  31 => 3,  28 => 2,  11 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "content/content.twig", "C:\\xampp\\htdocs\\Framework-Doc\\templates\\twig\\content\\content.twig");
    }
}
