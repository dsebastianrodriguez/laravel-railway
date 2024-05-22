<?php

namespace App\Swagger;

use ReflectionClass;
use ReflectionMethod;
use OpenApi\Annotations\Tag;
/**
 * @OA\Info(
 *     title="Api de estudiantes prueba en servidor LINUX",
 *     version="1.0",
 *     description="Listado de URL de la api de estudiantes"
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="apiUserAuth",
 *     type="apiKey",
 *     in="header",
 *     name="x-api-user"
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="apiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="x-api-key"
 * ),
 * @OA\Server(url="http://127.0.0.1:8000")
 */
class SwaggerDocumentation
{
    /**
     * Generate Swagger documentation.
     */
    public static function generate()
    {
        // Get all methods in the class
        $class = new ReflectionClass(self::class);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        // Get all tags defined in the class
        $tags = [];
        foreach ($methods as $method) {
            $docComment = $method->getDocComment();
            if ($docComment !== false) {
                $matches = [];
                preg_match_all('/@OA\\\(?:Tag|Tag\(.*?\))/', $docComment, $matches);
                foreach ($matches[0] as $match) {
                    $tag = eval("return $match;");
                    if ($tag instanceof Tag) {
                        $tags[] = $tag;
                    }
                }
            }
        }

        // Sort tags alphabetically
        usort($tags, function($a, $b) {
            return strcasecmp($a->name, $b->name);
        });

        // Add sorted tags back to the class annotations
        $docComment = '/**' . PHP_EOL;
        foreach ($tags as $tag) {
            $tagArray = json_decode(json_encode($tag), true);
            foreach ($tagArray as $key => $value) {
                $docComment .= " * @$key($value)" . PHP_EOL;
            }
        }
        $docComment .= ' */';

        // Write the doc comment to a temporary file and include it
        $tempFile = tempnam(sys_get_temp_dir(), 'swagger_docs');
        file_put_contents($tempFile, "<?php\n\nnamespace App\Swagger;\n\n/**\n$docComment\n */\n\nclass SwaggerDocumentation\n{\n}");

        include $tempFile;

        // Clean up the temporary file
        unlink($tempFile);
    }
}