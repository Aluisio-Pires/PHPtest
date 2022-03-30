<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<title>CEPfinder</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

		<script src="scripts/main.js"></script>
	</head>
	<body>
		
		<nav class="navbar navbar-light bg-light mb-4">
			<div class="container">
				<div class="navbar-brand mb-0 h1">
					<h3>CEPfinder</h3>
				</div>
			</div>
		</nav>
		

		<div class="container">
			<div>
				<h5 class="text-danger d-block" id="erro"></h5>
			</div>
			<form>
				<div class="row form-group">
					<div class="col-sm-3 mt-2">
						<input type="text" maxlength="10" class="form-control" placeholder="CEP" id="cep"/>
					</div>
					<div class="col-sm-9 mt-2">
						<input type="text" class="form-control" placeholder="Endereço" readonly id="logradouro" />
					</div>
				</div>

				<div class="row form-group">
					<div class="col-sm-6 mt-2">
						<input type="text" class="form-control" placeholder="Bairro" readonly id="bairro" />
					</div>
					<div class="col-sm-4 mt-2">
						<input type="text" class="form-control" placeholder="Cidade" readonly id="cidade" />
					</div>

					<div class="col-sm-2 mt-2">
						<input type="text" class="form-control" placeholder="UF" readonly id="uf" />
					</div>
				</div>

				<div>
					<button type="button" class="btn btn-info mt-2" onclick="verificaDados(cep.value)">Buscar</button>
				</div>
			</form>
			
		</div>
	</body>
</html>