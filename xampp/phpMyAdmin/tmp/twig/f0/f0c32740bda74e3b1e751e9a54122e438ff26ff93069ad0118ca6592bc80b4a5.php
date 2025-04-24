<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* transformation_overview.twig */
class __TwigTemplate_69e34b19b7fc7ef0ebbd1e31d41d2820ee8a937183e027b70eb00aa5c0b83ab4 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<h2>";
echo _gettext("Available media types");
        echo "</h2>

<ul>
  ";
        // line 4
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["mime_types"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["mime_type"]) {
            // line 5
            echo "    <li>
      ";
            // line 6
            echo ((twig_get_attribute($this->env, $this->source, $context["mime_type"], "is_empty", [], "any", false, false, false, 6)) ? ("<em>") : (""));
            echo "
      ";
            // line 7
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["mime_type"], "name", [], "any", false, false, false, 7), "html", null, true);
            echo "
      ";
            // line 8
            echo ((twig_get_attribute($this->env, $this->source, $context["mime_type"], "is_empty", [], "any", false, false, false, 8)) ? ("</em>") : (""));
            echo "
    </li>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['mime_type'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 11
        echo "</ul>

<h2 id=\"transformation\">";
echo _gettext("Available browser display transformations");
        // line 13
        echo "</h2>

<table class=\"table table-striped align-middle\">
  <thead>
    <tr>
      <th>";
echo _gettext("Browser display transformation");
        // line 18
        echo "</th>
      <th>";
echo _pgettext("for media type transformation", "Description");
        // line 19
        echo "</th>
    </tr>
  </thead>
  <tbody>
    ";
        // line 23
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["transformations"] ?? null), "transformation", [], "any", false, false, false, 23));
        foreach ($context['_seq'] as $context["_key"] => $context["transformation"]) {
            // line 24
            echo "      <tr>
        <td>";
            // line 25
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transformation"], "name", [], "any", false, false, false, 25), "html", null, true);
            echo "</td>
        <td>";
            // line 26
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transformation"], "description", [], "any", false, false, false, 26), "html", null, true);
            echo "</td>
      </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transformation'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 29
        echo "  </tbody>
</table>

<h2 id=\"input_transformation\">";
echo _gettext("Available input transformations");
        // line 32
        echo "</h2>

<table class=\"table table-striped align-middle\">
  <thead>
    <tr>
      <th>";
echo _gettext("Input transformation");
        // line 37
        echo "</th>
      <th>";
echo _pgettext("for media type transformation", "Description");
        // line 38
        echo "</th>
    </tr>
  </thead>
  <tbody>
    ";
        // line 42
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["transformations"] ?? null), "input_transformation", [], "any", false, false, false, 42));
        foreach ($context['_seq'] as $context["_key"] => $context["transformation"]) {
            // line 43
            echo "      <tr>
        <td>";
            // line 44
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transformation"], "name", [], "any", false, false, false, 44), "html", null, true);
            echo "</td>
        <td>";
            // line 45
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transformation"], "description", [], "any", false, false, false, 45), "html", null, true);
            echo "</td>
      </tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transformation'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 48
        echo "  </tbody>
</table>
";
    }

    public function getTemplateName()
    {
        return "transformation_overview.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  155 => 48,  146 => 45,  142 => 44,  139 => 43,  135 => 42,  129 => 38,  125 => 37,  117 => 32,  111 => 29,  102 => 26,  98 => 25,  95 => 24,  91 => 23,  85 => 19,  81 => 18,  73 => 13,  68 => 11,  59 => 8,  55 => 7,  51 => 6,  48 => 5,  44 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "transformation_overview.twig", "C:\\Users\\user\\Desktop\\GymProjekt\\xampp\\phpMyAdmin\\templates\\transformation_overview.twig");
    }
}
