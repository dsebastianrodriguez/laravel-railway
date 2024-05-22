<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *             title="Api de estudiantes prueba en servidor LINUX", 
 *             version="1.0",
 *             description="Listado de URL de la api de estudiantes"
 * ),
 * @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *         bearerFormat="JWT"
 *     ),
 * @OA\SecurityScheme(
 *     securityScheme="apiUserAuth",
 *     type="apiKey",
 *     in="header",
 *     name="x-api-user"
 *     ),
 * @OA\SecurityScheme(
 *     securityScheme="apiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="x-api-key"
 * ),
 * @OA\Server(url="http://127.0.0.1:8000")
 */

class studentController extends Controller
{
    /**
     * Listado de todos los estudiantes
     * @OA\Get (
     *     path="/api/student",
     *     tags={"Estudiantes"},
     * security={
     *         {"apiUserAuth": {}},
     *         {"apiKeyAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Aderson Felix"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="example@example.com"
     *                     ),
     *                     @OA\Property(
     *                         property="phone",
     *                         type="3112112111",
     *                         example="example@example.com"
     *                     ),
     *                     @OA\Property(
     *                         property="language",
     *                         type="string",
     *                         example="Spanish"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2024-02-23T00:09:16.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2024-02-23T12:33:45.000000Z"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $students = Student::all();
        if ($students->isEmpty()) {
            $data = [
                'message' => 'No data found',
                'status' => 200
            ];
            return  response()->json([$data]);
        } else {
            return response()->json($students, 200);
        }
    }

    /**
     * Almacenar un nuevo estudiante en la base de datos
     * @OA\Post(
     *     path="/api/student",
     *     tags={"Estudiantes"},
     *     summary="Almacenar un nuevo estudiante",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del estudiante a almacenar",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="language", type="string", example="English")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estudiante creado correctamente"),
     *             @OA\Property(property="status", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error en la validación de los datos"),
     *             @OA\Property(property="errors", type="object", example={"name": {"El campo nombre es obligatorio"}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error al crear el estudiante")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:student',
            'phone' => 'required|digits:10',
            'language' => 'required|in:English,Spanish,French'
        ]);

        //Si falla el validators
        if ($validator->fails()) {
            $data = [
                'message' => 'Error in validation of data',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return  response()->json($data, 400);
        }

        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'language' => $request->language
        ]);

        if (!$student) {
            $data = [
                'message' => 'Error creating the student',
                'status' => 500
            ];
            return  response()->json($data, 500);
        }

        $data = [
            'student' => $student,
            'status' => 201
        ];

        return   response()->json($data, 201);
    }

    /**
     * Mostrar la información de un estudiante
     * @OA\Get (
     *     path="/api/student/{id}",
     *     tags={"Estudiantes"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Aderson Felix"),
     *              @OA\Property(property="email", type="string", example="example@example.com"),
     *              @OA\Property(property="phone", type="string", example="3112112111"),
     *              @OA\Property(property="language", type="string", example="Spanish"),
     *              @OA\Property(property="created_at", type="string", example="2024-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2024-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Cliente] #id"),
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        $student = Student::find($id);
        if (!$student) {
            $data = [
                'message' => 'No data found',
                'status' => 404
            ];
            return  response()->json($data, 404);
        } else {
            $data = [
                'student' => $student,
                'status' => 200
            ];
            return response()->json($data, 200);
        }
    }

    /**
     * Eliminar un estudiante existente de la base de datos
     * @OA\Delete(
     *     path="/api/student/{id}",
     *     tags={"Estudiantes"},
     *     summary="Eliminar un estudiante existente",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estudiante eliminado exitosamente"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El estudiante no existe")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $student = Student::find($id);

        if (!$student) {
            $data = [
                "message" => "The student does not exist.",
                "status" => 404
            ];
            return response()->json($data, 404);
        }

        $student->delete();
        $data = [
            "message" => "Successfully deleted the student",
            "status" => 200
        ];

        return response()->json($data,  200);
    }

    /**
     * Actualizar un estudiante existente en la base de datos
     * @OA\Put(
     *     path="/api/student/{id}",
     *     tags={"Estudiantes"},
     *     summary="Actualizar un estudiante existente",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del estudiante a actualizar",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="language", type="string", example="English")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estudiante actualizado correctamente"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error en la validación de los datos"),
     *             @OA\Property(property="errors", type="object", example={"email": {"El correo electrónico ya está en uso"}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El estudiante no existe")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            $data = [
                "message" => "The student does not exist.",
                "status" => 404
            ];
            return response()->json($data, 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:student',
            'phone' => 'required|digits:10',
            'language' => 'required|in:English,Spanish,French'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error in validation data',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $student->name = $request->name;
        $student->email = $request->email;
        $student->phone = $request->phone;
        $student->language = $request->language;

        $student->save();
        $data = [
            "message" => "Student updated successfully!",
            "data" => $student,
            "status" => 200
        ];

        return response()->json($data, 200);
    }

    /**
     * Actualizar parcialmente un estudiante existente en la base de datos
     * @OA\Patch(
     *     path="/api/student/{id}",
     *     tags={"Estudiantes"},
     *     summary="Actualizar parcialmente un estudiante existente",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del estudiante a actualizar",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="language", type="string", example="English")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estudiante actualizado correctamente"),
     *             @OA\Property(property="status", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error en la validación de los datos"),
     *             @OA\Property(property="errors", type="object", example={"name": {"El campo nombre debe ser una cadena"}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se encontró ningún estudiante con el id proporcionado")
     *         )
     *     )
     * )
     */
    public function updatePartial(Request $request, $id)
    {
        $student = Student::find($id);
        if (!$student) {
            $data = [
                'message' => 'No student found with the given id!',
                'status' => 404
            ];
            return response()->json($data,  404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'max:100',
            'email' => 'email',
            'phone' => 'digit:10',
            'language' =>  'in:Spanish,English,French'
        ]);

        if ($validator->fails()) {
            $data = [
                'mesagge' => 'Error in validation data',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        if ($request->has('name')) {
            $student->name = $request->name;
        }
        if ($request->has('email')) {
            $student->email = $request->email;
        }
        if ($request->has('phone')) {
            $student->phone = $request->phone;
        }
        if ($request->has('language')) {
            $student->language = $request->language;
        }

        $student->save();

        $data = [
            'message' => 'Student updated successfully!',
            'student' => $student,
            'code'    => 200
        ];

        return response()->json($data, 200);
    }
}
