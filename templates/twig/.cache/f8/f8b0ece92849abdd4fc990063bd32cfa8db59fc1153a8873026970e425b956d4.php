<?php

/* overall/footer.twig */
class __TwigTemplate_9b479a09b674708deb22ab79358dde3a3dbdbefbefbcdfcdcf51473bf65b7593 extends Twig_Template
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
        echo "
    <!-- Footer -->
    <footer class=\"site-footer\">
      <div class=\"container\">
        <a id=\"scroll-up\" href=\"#\"><i class=\"fa fa-angle-up\"></i></a>

        <div class=\"row\">
          <div class=\"col-md-6 col-sm-6\">
            <p>Copyright &copy; ";
        // line 9
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "Y"), "html", null, true);
        echo " <a href=\"http://ocrend.com\" target=\"_blank\">Ocrend Software</a>. Todos los derechos reservados.</p>
              <a class=\"github-button\" href=\"https://github.com/prinick96/Ocrend-Framework\" data-icon=\"octicon-eye\" data-style=\"mega\" data-count-href=\"/prinick96/Ocrend-Framework/watchers\" data-count-api=\"/repos/prinick96/Ocrend-Framework#subscribers_count\" data-count-aria-label=\"# watchers on GitHub\" aria-label=\"Watch prinick96/Ocrend-Framework on GitHub\">Watch</a>
              <a class=\"github-button\" href=\"https://github.com/prinick96/Ocrend-Framework\" data-icon=\"octicon-star\" data-style=\"mega\" data-count-href=\"/prinick96/Ocrend-Framework/stargazers\" data-count-api=\"/repos/prinick96/Ocrend-Framework#stargazers_count\" data-count-aria-label=\"# stargazers on GitHub\" aria-label=\"Star prinick96/Ocrend-Framework on GitHub\">Star</a>
              <a class=\"github-button\" href=\"https://github.com/prinick96/Ocrend-Framework/fork\" data-icon=\"octicon-repo-forked\" data-style=\"mega\" data-count-href=\"/prinick96/Ocrend-Framework/network\" data-count-api=\"/repos/prinick96/Ocrend-Framework#forks_count\" data-count-aria-label=\"# forks on GitHub\" aria-label=\"Fork prinick96/Ocrend-Framework on GitHub\">Fork</a>
              <a class=\"github-button\" href=\"https://github.com/prinick96/Ocrend-Framework/archive/master.zip\" data-icon=\"octicon-cloud-download\" data-style=\"mega\" aria-label=\"Download prinick96/Ocrend-Framework on GitHub\">Download</a>
          </div>
          <div class=\"col-md-6 col-sm-6\">
            <ul class=\"footer-menu\">
              <li><a href=\"https://www.youtube.com/playlist?list=PLDQZoQpLCoUBVbDUxr8cxVX0ik8uh5WBh\" target=\"_blank\">Documentación YouTube</a></li>
              <li><a href=\"https://github.com/prinick96/Ocrend-Framework/wiki\" target=\"_blank\">Wiki</a></li>
              <li><a href=\"https://github.com/prinick96/Ocrend-Framework/\" target=\"_blank\">Repositorio</a></li>
              <li><a href=\"http://ocrend.com/contacto/\" target=\"_blank\">Nuestros servicios</a></li>
              <li>
                <form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\" target=\"_top\">
                  <input type=\"hidden\" name=\"cmd\" value=\"_s-xclick\">
                  <input type=\"hidden\" name=\"hosted_button_id\" value=\"TZPT7WZYMW6BW\">
                  <button class=\"btn btn-info\" type=\"submit\" name=\"submit\" alt=\"Contribuye con lo que desees <3\">Donaciones</button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </footer>
    <!-- END Footer -->

    <!-- Scripts -->
    <script src=\"views/assets/js/theDocs.all.min.js\"></script>
    <script src=\"views/assets/js/theDocs.js\"></script>
    <script src=\"views/assets/js/custom.js\"></script>
";
    }

    public function getTemplateName()
    {
        return "overall/footer.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  29 => 9,  19 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "overall/footer.twig", "C:\\xampp\\htdocs\\Framework-Doc\\templates\\twig\\overall\\footer.twig");
    }
}
