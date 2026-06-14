import os
root = os.path.abspath(os.path.dirname(__file__))
files = [
    'ACTUALIZACION_PERSISTENCIA_DATOS.md',
    'CUMPLIMIENTO_SOF109.md',
    'DOCUMENTACION_TECNICA.md',
    'ER_Diagram.d2',
    'ERD_DIAGRAM.txt',
    'database.sql',
    'import.sql'
]
print('cwd', root)
for f in files:
    path = os.path.join(root, f)
    if os.path.exists(path):
        os.remove(path)
        print('deleted', f)
    else:
        print('missing', f)
