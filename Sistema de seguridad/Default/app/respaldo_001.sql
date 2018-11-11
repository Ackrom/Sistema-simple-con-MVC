--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.4
-- Dumped by pg_dump version 9.5.4

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: actualizarusuario(character varying, character varying, integer, integer, character varying, character varying, character varying, character varying, character, character, character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION actualizarusuario(usua character varying, pass character varying, permisos integer, pcedula integer, primernombre character varying, primerapellido character varying, segundonombre character varying, segundoapellido character varying, sexo character, nacionalidad character, cargo character varying, departamento character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$

DECLARE
 V_Individuo integer;                                 
 V_Personal integer;
 
BEGIN

 Select ID_Individuo FROM Individuos WHERE Cedula=PCedula INTO V_Individuo;
 Select ID_Personal FROM Personal WHERE ID_Individuo = V_Individuo INTO V_Personal;

 DELETE FROM Usuarios WHERE ID_Personal = V_Personal;
 DELETE FROM Personal WHERE ID_Individuo = V_Individuo;
 DELETE FROM Individuos WHERE Cedula=Pcedula;
 
 INSERT INTO Individuos VALUES (V_Individuo, Pcedula, primernombre, primerapellido, segundonombre, segundoapellido,sexo,nacionalidad);
 INSERT INTO Personal VALUES (V_Personal, V_Individuo, cargo, departamento);
 INSERT INTO Usuarios VALUES (usua, V_Personal, pass, permisos, 1);
 
END;
$$;


ALTER FUNCTION public.actualizarusuario(usua character varying, pass character varying, permisos integer, pcedula integer, primernombre character varying, primerapellido character varying, segundonombre character varying, segundoapellido character varying, sexo character, nacionalidad character, cargo character varying, departamento character varying) OWNER TO postgres;

--
-- Name: almacenarauditoria(character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION almacenarauditoria(usuario character varying, descripcion character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$

begin
 INSERT INTO Auditorias VALUES ((select (COALESCE(MAX(ID_Auditoria),-1)+1) from view_auditorias), usuario, descripcion, now());
end;
$$;


ALTER FUNCTION public.almacenarauditoria(usuario character varying, descripcion character varying) OWNER TO postgres;

--
-- Name: busquedacaucionessimple(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION busquedacaucionessimple(texto character varying) RETURNS SETOF record
    LANGUAGE plpgsql
    AS $$

BEGIN
 
IF COALESCE(texto,'e') = 'e' OR texto = '' THEN

 RETURN QUERY SELECT
 Cauc.Dic_Imagen,
	Ind.Cedula,
	Ind.P_nombre,
	Ind.P_apellido,
	Deli.Desc_Delito,
	Fich.Fecha_Creacion
	FROM 
	view_individuos AS Ind INNER JOIN view_fichas AS Fich ON Fich.Cedula = Ind.Cedula INNER JOIN view_cauciones AS Cauc ON Cauc.ID_Ficha = Fich.ID_Ficha INNER JOIN view_delitos AS Deli ON Fich.ID_Delito = Deli.ID_Delito;

ELSE

 RETURN QUERY SELECT
 Cauc.Dic_Imagen,
	Ind.Cedula,
	Ind.P_nombre,
	Ind.P_apellido,
	Deli.Desc_Delito,
	Fich.Fecha_Creacion
	FROM 
	view_individuos AS Ind INNER JOIN view_fichas AS Fich ON Fich.Cedula = Ind.Cedula INNER JOIN view_cauciones AS Cauc ON Cauc.ID_Ficha = Fich.ID_Ficha INNER JOIN view_delitos AS Deli ON Fich.ID_Delito = Deli.ID_Delito AND to_tsvector(coalesce(Ind.P_nombre,'') || ' ' || COALESCE(Ind.P_apellido,'') || ' ' || COALESCE(cast(Ind.cedula AS text),'')) @@ to_tsquery(regexp_replace(cast(plainto_tsquery(texto) as text), E'(\'\\w+\')', E'\\1:*', 'g'));

END IF; 

 RETURN;
END;
$$;


ALTER FUNCTION public.busquedacaucionessimple(texto character varying) OWNER TO postgres;

--
-- Name: caucionmain(integer, integer, character varying, character varying, character varying, character varying, character, character, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, date, character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION caucionmain(indocumentado integer, scedula integer, pn character varying, pa character varying, sn character varying, sa character varying, sexo character, nacion character, parro integer, lug character varying, parrot integer, lugt character varying, num_delito integer, descdelito character varying, telef character varying, edc character varying, profe character varying, fechan date, img character varying, num integer) RETURNS void
    LANGUAGE plpgsql
    AS $$

DECLARE
Indo integer;
FN integer;

 BEGIN 
 
  Select (LEAST(COALESCE(MIN(Ind.Cedula),1),0)-1) from view_Individuos AS Ind INTO Indo;
  Select (COALESCE(MAX(ID_Ficha),-1)+1) from view_Fichas INTO FN;

  IF Indocumentado = 1 THEN
  
  PERFORM RegistrarIndividuo(Indo,PN,PA,SN,SA,Sexo,Nacion,Parro,Lug,ParroT,LugT);
  PERFORM RegistrarFicha(Indo,Num_Delito,DescDelito,Telef,EdC,Profe,(CAST(FechaN AS Date)),null);
  PERFORM RegistrarCaucion(FN,IMG,Num);

  ELSE
  
   IF exists(Select * from individuos AS Ind WHERE Ind.cedula = SCedula) THEN
   
   PERFORM RegistrarFicha(SCedula,Num_Delito,DescDelito,Telef,EdC,Profe,(CAST(FechaN AS Date)),null);
   PERFORM RegistrarCaucion(FN,IMG,Num);
   
   ELSE 
   
   PERFORM RegistrarIndividuo(SCedula,PN,PA,SN,SA,Sexo,Nacion,Parro,Lug,ParroT,LugT);
   PERFORM RegistrarFicha(SCedula,Num_Delito,DescDelito,Telef,EdC,Profe,(CAST(FechaN AS Date)),null);
   PERFORM RegistrarCaucion(FN,IMG,Num);
  
   END IF;
  END IF;
 
 END;
$$;


ALTER FUNCTION public.caucionmain(indocumentado integer, scedula integer, pn character varying, pa character varying, sn character varying, sa character varying, sexo character, nacion character, parro integer, lug character varying, parrot integer, lugt character varying, num_delito integer, descdelito character varying, telef character varying, edc character varying, profe character varying, fechan date, img character varying, num integer) OWNER TO postgres;

--
-- Name: consultarauditoria(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION consultarauditoria() RETURNS SETOF record
    LANGUAGE plpgsql
    AS $$

BEGIN
 RETURN QUERY Select * FROM view_auditorias ORDER BY ID_Auditoria DESC;
 RETURN;
END;
$$;


ALTER FUNCTION public.consultarauditoria() OWNER TO postgres;

--
-- Name: datosdetenido(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION datosdetenido(dcedu integer) RETURNS SETOF record
    LANGUAGE plpgsql
    AS $$

DECLARE
IDFi integer;
IMG varchar;
Cedula integer;
Sexo char;
Nacionalidad char;
PrimerNombre varchar;
PrimerApellido varchar;
SegundoNombre varchar;
SegundoApellido varchar;
CantidadDelitos integer;
Delito varchar ARRAY;
DescDelito varchar ARRAY;
FechaC varchar ARRAY;
FechaN Date;
EstadoCivil varchar;
Profesion varchar;
Telef varchar;
EstadoCasa varchar;
IDCasa integer;
MunicipioCasa varchar;
ParroquiaCasa varchar;
LugarCasa varchar;
IDTrabajo integer;
EstadoT varchar;
MunicipioT varchar;
ParroquiaT varchar;
LugarT varchar;

 BEGIN 
  
  SELECT Fich.ID_Ficha  FROM view_fichas AS Fich WHERE Fich.cedula = DCedu LIMIT 1 INTO IDFi;
   
  SELECT Cauc.dic_imagen       FROM view_cauciones AS Cauc INNER JOIN view_fichas AS Fich ON Fich.ID_Ficha = Cauc.ID_Ficha AND Fich.ID_Ficha = IDFi AND Fich.cedula = DCedu INTO IMG;
  SELECT DCedu INTO Cedula;
  SELECT Ind.Sexo              FROM view_individuos AS Ind WHERE Ind.Cedula = DCedu INTO Sexo;
  SELECT Ind.Nacionalidad      FROM view_individuos AS Ind WHERE Ind.Cedula = DCedu INTO Nacionalidad;
  SELECT Ind.p_nombre          FROM view_individuos AS Ind WHERE Ind.Cedula = DCedu INTO PrimerNombre;
  SELECT Ind.p_apellido        FROM view_individuos AS Ind WHERE Ind.Cedula = DCedu INTO PrimerApellido;
  SELECT Ind.s_nombre          FROM view_individuos AS Ind WHERE Ind.Cedula = DCedu INTO SegundoNombre;
  SELECT Ind.s_apellido        FROM view_individuos AS Ind WHERE Ind.Cedula = DCedu INTO SegundoApellido;
  SELECT COUNT(Fich.ID_Delito) FROM view_fichas AS Fich WHERE Fich.cedula = DCedu INTO CantidadDelitos;
  
  FOR i IN 0 .. CantidadDelitos LOOP
  FechaC[i]:= Fich.Fecha_Creacion FROM view_fichas AS Fich WHERE Fich.cedula = DCedu LIMIT 1 OFFSET i;
  Delito[i]:= Deli.Desc_Delito FROM view_delitos AS Deli INNER JOIN view_fichas AS Fich ON Fich.ID_Delito = Deli.ID_Delito AND Fich.cedula = DCedu LIMIT 1 OFFSET i;
  DescDelito[i]:= Fich.Desc_Delito FROM view_fichas AS Fich WHERE Fich.cedula = DCedu LIMIT 1 OFFSET i;
  END LOOP;
  
  SELECT Fich.Fecha_Nacimiento FROM view_fichas AS Fich WHERE Fich.cedula = DCedu AND Fich.ID_Ficha = IDFi INTO FechaN;
  SELECT Fich.Edo_Civil        FROM view_fichas AS Fich WHERE Fich.cedula = DCedu AND Fich.ID_Ficha = IDFi INTO EstadoCivil;
  SELECT Fich.Profesion        FROM view_fichas AS Fich WHERE Fich.cedula = DCedu AND Fich.ID_Ficha = IDFi INTO Profesion;
  SELECT Fich.Nro_Telefono     FROM view_fichas AS Fich WHERE Fich.cedula = DCedu AND Fich.ID_Ficha = IDFi INTO Telef;
  SELECT Dire.ID_Direccion     FROM view_direcciones AS Dire WHERE Dire.cedula = DCedu LIMIT 1 INTO IDCasa;
  SELECT Dire.Lugar            FROM view_direcciones AS Dire WHERE Dire.ID_Direccion = IDCasa INTO LugarCasa;
  SELECT Parro.Nombre          FROM view_parroquias AS Parro INNER JOIN view_direcciones AS Dire ON Dire.ID_Parroquia = Parro.ID_Parroquia AND Dire.ID_Direccion = IDCasa INTO ParroquiaCasa;
  SELECT Muni.Nombre           FROM view_municipios AS Muni INNER JOIN view_parroquias AS Parro ON Parro.ID_Municipio = Muni.ID_Municipio INNER JOIN view_direcciones AS Dire ON Dire.ID_Parroquia = Parro.ID_Parroquia AND Dire.ID_Direccion = IDCasa INTO MunicipioCasa;
  SELECT Est.Nombre            FROM view_estados AS Est INNER JOIN view_municipios AS Muni ON Muni.ID_Estado = Est.ID_Estado INNER JOIN view_parroquias AS Parro ON Parro.ID_Municipio = Muni.ID_Municipio INNER JOIN view_direcciones AS Dire ON Dire.ID_Parroquia = Parro.ID_Parroquia AND Dire.ID_Direccion = IDCasa INTO EstadoCasa;

  IF exists(Select * from view_direcciones AS Dire WHERE Dire.cedula = DCedu OFFSET 1) THEN
  
  SELECT Dire.ID_Direccion     FROM view_direcciones AS Dire WHERE Dire.cedula = DCedu OFFSET 1 INTO IDTrabajo;
  SELECT Dire.Lugar            FROM view_direcciones AS Dire WHERE Dire.ID_Direccion = IDTrabajo INTO LugarT;
  SELECT Parro.Nombre          FROM view_parroquias AS Parro INNER JOIN view_direcciones AS Dire ON Dire.ID_Parroquia = Parro.ID_Parroquia AND Dire.ID_Direccion = IDTrabajo INTO ParroquiaT;
  SELECT Muni.Nombre           FROM view_municipios AS Muni INNER JOIN view_parroquias AS Parro ON Parro.ID_Municipio = Muni.ID_Municipio INNER JOIN view_direcciones AS Dire ON Dire.ID_Parroquia = Parro.ID_Parroquia AND Dire.ID_Direccion = IDTrabajo INTO MunicipioT;
  SELECT Est.Nombre            FROM view_estados AS Est INNER JOIN view_municipios AS Muni ON Muni.ID_Estado = Est.ID_Estado INNER JOIN view_parroquias AS Parro ON Parro.ID_Municipio = Muni.ID_Municipio INNER JOIN view_direcciones AS Dire ON Dire.ID_Parroquia = Parro.ID_Parroquia AND Dire.ID_Direccion = IDTrabajo INTO EstadoT;

  ELSE
  
  SELECT null INTO EstadoT;
  SELECT null INTO MunicipioT;
  SELECT null INTO ParroquiaT;
  SELECT null INTO LugarT;

  END IF;
  
  RETURN QUERY SELECT IMG,Cedula,Sexo,Nacionalidad,PrimerNombre,PrimerApellido,SegundoNombre,SegundoApellido,CantidadDelitos,(array_to_string(Delito,'/')),(array_to_string(DescDelito,'/')),(array_to_string(FechaC,'/')),FechaN,EstadoCivil,Profesion,Telef,EstadoCasa,MunicipioCasa,ParroquiaCasa,LugarCasa,EstadoT,MunicipioT,ParroquiaT,LugarT;
  RETURN;	
 END;
$$;


ALTER FUNCTION public.datosdetenido(dcedu integer) OWNER TO postgres;

--
-- Name: datosusuario(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION datosusuario(usua character varying) RETURNS SETOF record
    LANGUAGE plpgsql
    AS $$

DECLARE 
 Nombre varchar;
 Pass varchar;
 PrimerNombre varchar;
 PrimerApellido varchar;
 SegundoNombre varchar;
 SegundoApellido varchar;
 Cedula integer;

BEGIN

 Select Usu.nombre          from view_usuarios AS Usu WHERE Usu.nombre=usua INTO Nombre;
 Select Usu.Contraseña      from view_usuarios AS Usu WHERE Usu.nombre=usua INTO Pass;
 Select Iv.P_Nombre         from view_usuarios AS Usu INNER JOIN view_personal as Per ON Usu.Nombre = usua AND Usu.ID_Personal = Per.ID_Personal INNER JOIN view_individuos as Iv ON Per.ID_Individuo = Iv.ID_Individuo INTO PrimerNombre;
 Select Iv.P_Apellido       from view_usuarios AS Usu INNER JOIN view_personal as Per ON Usu.Nombre = usua AND Usu.ID_Personal = Per.ID_Personal INNER JOIN view_individuos as Iv ON Per.ID_Individuo = Iv.ID_Individuo INTO PrimerApellido;
 Select Iv.S_Nombre         from view_usuarios AS Usu INNER JOIN view_personal as Per ON Usu.Nombre = usua AND Usu.ID_Personal = Per.ID_Personal INNER JOIN view_individuos as Iv ON Per.ID_Individuo = Iv.ID_Individuo INTO SegundoNombre;
 Select Iv.S_Apellido       from view_usuarios AS Usu INNER JOIN view_personal as Per ON Usu.Nombre = usua AND Usu.ID_Personal = Per.ID_Personal INNER JOIN view_individuos as Iv ON Per.ID_Individuo = Iv.ID_Individuo INTO SegundoApellido;
 Select Iv.Cedula           from view_usuarios AS Usu INNER JOIN view_personal as Per ON Usu.Nombre = usua AND Usu.ID_Personal = Per.ID_Personal INNER JOIN view_individuos as Iv ON Per.ID_Individuo = Iv.ID_Individuo INTO Cedula;
 
 RETURN QUERY Select Nombre,Pass,PrimerNombre,SegundoNombre,PrimerApellido,SegundoApellido,Cedula;
 RETURN;
END;


$$;


ALTER FUNCTION public.datosusuario(usua character varying) OWNER TO postgres;

--
-- Name: eliminarusuario(character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION eliminarusuario(eusua character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN 
  UPDATE Usuarios SET Activo=0 WHERE Nombre=Eusua;
END;
$$;


ALTER FUNCTION public.eliminarusuario(eusua character varying) OWNER TO postgres;

--
-- Name: listaindocumentados(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION listaindocumentados() RETURNS SETOF record
    LANGUAGE plpgsql
    AS $$
                                            
 BEGIN 
  RETURN QUERY SELECT Cauc.dic_imagen,
                      Deli.desc_delito,
                      Ind.p_nombre,
                      Ind.p_apellido,
                      Ind.cedula,
                      Fich.fecha_creacion
               FROM view_cauciones AS Cauc INNER JOIN view_fichas AS Fich ON Cauc.ID_Ficha = Fich.ID_Ficha INNER JOIN view_delitos AS Deli ON Fich.ID_Delito = Deli.ID_Delito INNER JOIN view_individuos AS Ind ON Fich.cedula = Ind.cedula AND Ind.cedula < 0;

  RETURN;
 END;
$$;


ALTER FUNCTION public.listaindocumentados() OWNER TO postgres;

--
-- Name: login(character varying, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION login(usua character varying, pass character varying) RETURNS integer
    LANGUAGE plpgsql
    AS $$

 DECLARE
  Nivel integer;

begin
  select Lvl_Permisos INTO Nivel from view_USUARIOS WHERE nombre=usua AND contraseña=pass AND Activo=1;
  RETURN Nivel;
end;
$$;


ALTER FUNCTION public.login(usua character varying, pass character varying) OWNER TO postgres;

--
-- Name: notificacionmain(integer, character varying, character varying, character varying, character varying, character, character, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, date, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION notificacionmain(scedula integer, pn character varying, pa character varying, sn character varying, sa character varying, sexo character, nacion character, parro integer, lug character varying, parrot integer, lugt character varying, num_delito integer, descdelito character varying, telef character varying, edc character varying, profe character varying, fechan date, numfich integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
                                            
 BEGIN 
  
  IF exists(Select * from individuos AS Ind WHERE Ind.cedula = SCedula) THEN
  
   PERFORM RegistrarFicha(SCedula,Num_Delito,DescDelito,Telef,EdC,Profe,(CAST(FechaN AS Date)),NumFich);
  
  ELSE
  
   PERFORM RegistrarIndividuo(SCedula,PN,PA,SN,SA,Sexo,Nacion,Parro,Lug,ParroT,LugT);
   PERFORM RegistrarFicha(SCedula,Num_Delito,DescDelito,Telef,EdC,Profe,(CAST(FechaN AS Date)),NumFich);
   
  END IF;
  
 END;
$$;


ALTER FUNCTION public.notificacionmain(scedula integer, pn character varying, pa character varying, sn character varying, sa character varying, sexo character, nacion character, parro integer, lug character varying, parrot integer, lugt character varying, num_delito integer, descdelito character varying, telef character varying, edc character varying, profe character varying, fechan date, numfich integer) OWNER TO postgres;

--
-- Name: registrarcaucion(integer, character varying, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION registrarcaucion(fich integer, img character varying, num integer) RETURNS void
    LANGUAGE plpgsql
    AS $$

 BEGIN 
  INSERT INTO Cauciones VALUES((select(COALESCE(MAX(ID_Caucion),-1)+1) from view_Cauciones),Fich,IMG,Num);
 END;
$$;


ALTER FUNCTION public.registrarcaucion(fich integer, img character varying, num integer) OWNER TO postgres;

--
-- Name: registrarficha(integer, integer, character varying, character varying, character varying, character varying, date, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION registrarficha(cedula integer, num_delito integer, descdelito character varying, telef character varying, edc character varying, profe character varying, fechan date, numfich integer) RETURNS void
    LANGUAGE plpgsql
    AS $$

DECLARE
TLF varchar;
Prf varchar;
NF integer;

 BEGIN 
 
  IF COALESCE(Telef,'e') = 'e' THEN
   Select null INTO TLF;
  ELSE
   Select Telef INTO TLF;
  END IF;

  IF COALESCE(Profe,'e') = 'e' THEN
   Select null INTO Prf;
  ELSE
   Select Profe INTO Prf;
  END IF;

  IF NumFich < 0 THEN
   Select null INTO NF;
  ELSE
   Select NumFich INTO NF;
  END IF;

  INSERT INTO Fichas VALUES ((select(COALESCE(MAX(ID_Ficha),-1)+1) from view_Fichas), Num_Delito, Cedula, DescDelito, TLF, EdC, Prf, now(), (CAST(FechaN AS Date)), NF);
 END;
$$;


ALTER FUNCTION public.registrarficha(cedula integer, num_delito integer, descdelito character varying, telef character varying, edc character varying, profe character varying, fechan date, numfich integer) OWNER TO postgres;

--
-- Name: registrarindividuo(integer, character varying, character varying, character varying, character varying, character, character, integer, character varying, integer, character varying); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION registrarindividuo(cedula integer, pn character varying, pa character varying, sn character varying, sa character varying, sexo character, nacion character, parro integer, lug character varying, parrot integer, lugt character varying) RETURNS void
    LANGUAGE plpgsql
    AS $$

DECLARE
SNs varchar;
SAs varchar;

 BEGIN 
 
  IF COALESCE(SN,'e') = 'e' THEN
   Select null INTO SNs;
  ELSE
   Select SN INTO SNs;
  END IF;

  IF COALESCE(SA,'e') = 'e' THEN
   Select null INTO SAs;
  ELSE
    Select SA INTO SAs;
  END IF;
  

  INSERT INTO Individuos VALUES (Cedula, PN, PA, SNs, SAs, Sexo, Nacion);
  INSERT INTO Direcciones VALUES ((select(COALESCE(MAX(ID_Direccion),-1)+1) from view_Direcciones),Parro,Cedula,Lug);

  IF ParroT > -1 THEN
   INSERT INTO Direcciones VALUES ((select(COALESCE(MAX(ID_Direccion),-1)+1) from view_Direcciones),ParroT,Cedula,LugT);
  END IF;
  

 END;
$$;


ALTER FUNCTION public.registrarindividuo(cedula integer, pn character varying, pa character varying, sn character varying, sa character varying, sexo character, nacion character, parro integer, lug character varying, parrot integer, lugt character varying) OWNER TO postgres;

--
-- Name: registro(character varying, character varying, character varying, character varying, character varying, character varying, character, character, character varying, character varying, integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION registro(usua character varying, pass character varying, primernombre character varying, segundonombre character varying, segundoapellido character varying, primerapellido character varying, sexo character, nacionalidad character, cargo character varying, departamento character varying, cedula integer, permiso integer) RETURNS SETOF record
    LANGUAGE plpgsql
    AS $$
DECLARE
 Resultado Boolean;
 Permisos integer;
 V_Personal integer;
 
BEGIN
 
 IF exists(select * from view_USUARIOS WHERE Nombre=usua) THEN --Verifica si el nombre de usuario ya esta registrado

  Select False INTO Resultado;
  Select Lvl_Permisos FROM view_USUARIOS WHERE Nombre=usua INTO Permisos;
 
 ELSE
 
  Select True INTO Resultado;
  Select permiso INTO Permisos;
  Select (select (COALESCE(MAX(ID_Personal),-1)+1) from view_PERSONAL) INTO V_Personal;
  
  INSERT INTO INDIVIDUOS(cedula, p_nombre, p_apellido, s_nombre, s_apellido,sexo, nacionalidad) 
  VALUES (cedula, primernombre, primerapellido, segundonombre, segundoapellido,sexo,nacionalidad);
  
  INSERT INTO Personal VALUES (V_Personal, cedula, cargo, departamento);
  INSERT INTO Usuarios VALUES (usua, V_Personal, pass, permiso, 1);
  
 END IF;
 

RETURN QUERY Select Resultado,Permisos; --Aqui retorna 2 datos distintos de tipos distintos. por eso hago que la función devuelva SETOF RECORD
RETURN;
END;
$$;


ALTER FUNCTION public.registro(usua character varying, pass character varying, primernombre character varying, segundonombre character varying, segundoapellido character varying, primerapellido character varying, sexo character, nacionalidad character, cargo character varying, departamento character varying, cedula integer, permiso integer) OWNER TO postgres;

--
-- Name: usuariosregistrados(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION usuariosregistrados(total integer, indice integer) RETURNS SETOF record
    LANGUAGE plpgsql
    AS $$

DECLARE 
 Nombre varchar;
 Pass varchar;
 PrimerNombre varchar;
 PrimerApellido varchar;
 Cedula integer;
 rec RECORD;
 
begin

 FOR rec IN Select v.Nombre from view_usuarios AS v WHERE v.Activo=1 ORDER BY v.Nombre ASC LIMIT total OFFSET indice-1 LOOP
 
 Select Usu.nombre          from view_usuarios AS Usu WHERE Usu.nombre=rec.Nombre INTO Nombre;
 Select Usu.Contraseña      from view_usuarios AS Usu WHERE Usu.nombre=rec.Nombre INTO Pass;
 Select Iv.P_Nombre         from view_usuarios AS Usu INNER JOIN view_personal AS Per ON Usu.Nombre = rec.Nombre AND Usu.ID_Personal = Per.ID_Personal INNER JOIN view_individuos as Iv ON Per.ID_Individuo = Iv.ID_Individuo INTO PrimerNombre;
 Select Iv.P_Apellido       from view_usuarios AS Usu INNER JOIN view_personal AS Per ON Usu.Nombre = rec.Nombre AND Usu.ID_Personal = Per.ID_Personal INNER JOIN view_individuos as Iv ON Per.ID_Individuo = Iv.ID_Individuo INTO PrimerApellido;
 Select Iv.Cedula           from view_usuarios AS Usu INNER JOIN view_personal AS Per ON Usu.Nombre = rec.Nombre AND Usu.ID_Personal = Per.ID_Personal INNER JOIN view_individuos as Iv ON Per.ID_Individuo = Iv.ID_Individuo INTO Cedula;

 Select Nombre,Pass,PrimerNombre,PrimerApellido,Cedula INTO rec;
 RETURN NEXT rec;
 
 END LOOP;
 
 RETURN;
 
end;
$$;


ALTER FUNCTION public.usuariosregistrados(total integer, indice integer) OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: auditorias; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE auditorias (
    id_auditoria integer NOT NULL,
    nombre character varying(30),
    descripcion character varying(200) NOT NULL,
    fecha timestamp without time zone NOT NULL
);


ALTER TABLE auditorias OWNER TO postgres;

--
-- Name: cauciones; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE cauciones (
    id_caucion integer NOT NULL,
    id_ficha integer,
    dic_imagen character varying(200) NOT NULL,
    num_caucion integer NOT NULL
);


ALTER TABLE cauciones OWNER TO postgres;

--
-- Name: delitos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE delitos (
    id_delito integer NOT NULL,
    desc_delito character varying(160)
);


ALTER TABLE delitos OWNER TO postgres;

--
-- Name: direcciones; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE direcciones (
    id_direccion integer NOT NULL,
    id_parroquia integer NOT NULL,
    cedula integer NOT NULL,
    lugar character varying(160) NOT NULL
);


ALTER TABLE direcciones OWNER TO postgres;

--
-- Name: estados; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE estados (
    nombre character varying(60) NOT NULL,
    id_estado integer NOT NULL
);


ALTER TABLE estados OWNER TO postgres;

--
-- Name: fichas; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE fichas (
    id_ficha integer NOT NULL,
    id_delito integer NOT NULL,
    cedula integer NOT NULL,
    desc_delito character varying(200),
    nro_telefono character varying(20),
    edo_civil character varying(15) NOT NULL,
    profesion character varying(60),
    fecha_creacion date NOT NULL,
    fecha_nacimiento date NOT NULL,
    num_ficha integer
);


ALTER TABLE fichas OWNER TO postgres;

--
-- Name: individuos; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE individuos (
    cedula integer NOT NULL,
    p_nombre character varying(60) NOT NULL,
    p_apellido character varying(60) NOT NULL,
    s_nombre character varying(60),
    s_apellido character varying(60),
    sexo character(1) NOT NULL,
    nacionalidad character(1) NOT NULL
);


ALTER TABLE individuos OWNER TO postgres;

--
-- Name: modificaciones; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE modificaciones (
    id_mod integer NOT NULL,
    cedula integer NOT NULL,
    nuevacedula integer,
    pnombre character varying(60),
    papellido character varying(60),
    snombre character varying(60),
    sapellido character varying(60)
);


ALTER TABLE modificaciones OWNER TO postgres;

--
-- Name: municipios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE municipios (
    id_municipio integer NOT NULL,
    id_estado integer,
    nombre character varying(60) NOT NULL
);


ALTER TABLE municipios OWNER TO postgres;

--
-- Name: parroquias; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE parroquias (
    id_parroquia integer NOT NULL,
    id_municipio integer,
    nombre character varying(60) NOT NULL
);


ALTER TABLE parroquias OWNER TO postgres;

--
-- Name: personal; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE personal (
    id_personal integer NOT NULL,
    cedula integer,
    cargo character varying(30) NOT NULL,
    departamento character varying(40) NOT NULL
);


ALTER TABLE personal OWNER TO postgres;

--
-- Name: usuarios; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE usuarios (
    nombre character varying(30) NOT NULL,
    id_personal integer,
    "contraseña" character varying(30) NOT NULL,
    lvl_permisos integer NOT NULL,
    activo integer NOT NULL
);


ALTER TABLE usuarios OWNER TO postgres;

--
-- Name: view_auditorias; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_auditorias AS
 SELECT auditorias.id_auditoria,
    auditorias.nombre,
    auditorias.descripcion,
    auditorias.fecha
   FROM auditorias;


ALTER TABLE view_auditorias OWNER TO postgres;

--
-- Name: view_cauciones; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_cauciones AS
 SELECT cauciones.id_caucion,
    cauciones.id_ficha,
    cauciones.dic_imagen,
    cauciones.num_caucion
   FROM cauciones;


ALTER TABLE view_cauciones OWNER TO postgres;

--
-- Name: view_delitos; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_delitos AS
 SELECT delitos.id_delito,
    delitos.desc_delito
   FROM delitos;


ALTER TABLE view_delitos OWNER TO postgres;

--
-- Name: view_direcciones; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_direcciones AS
 SELECT direcciones.id_direccion,
    direcciones.id_parroquia,
    direcciones.cedula,
    direcciones.lugar
   FROM direcciones;


ALTER TABLE view_direcciones OWNER TO postgres;

--
-- Name: view_estados; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_estados AS
 SELECT estados.nombre,
    estados.id_estado
   FROM estados;


ALTER TABLE view_estados OWNER TO postgres;

--
-- Name: view_fichas; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_fichas AS
 SELECT fichas.id_ficha,
    fichas.id_delito,
    fichas.cedula,
    fichas.desc_delito,
    fichas.nro_telefono,
    fichas.edo_civil,
    fichas.profesion,
    fichas.fecha_creacion,
    fichas.fecha_nacimiento,
    fichas.num_ficha
   FROM fichas;


ALTER TABLE view_fichas OWNER TO postgres;

--
-- Name: view_individuos; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_individuos AS
 SELECT individuos.cedula,
    individuos.p_nombre,
    individuos.p_apellido,
    individuos.s_nombre,
    individuos.s_apellido,
    individuos.sexo,
    individuos.nacionalidad
   FROM individuos;


ALTER TABLE view_individuos OWNER TO postgres;

--
-- Name: view_modificaciones; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_modificaciones AS
 SELECT modificaciones.id_mod,
    modificaciones.cedula,
    modificaciones.nuevacedula,
    modificaciones.pnombre,
    modificaciones.papellido,
    modificaciones.snombre,
    modificaciones.sapellido
   FROM modificaciones;


ALTER TABLE view_modificaciones OWNER TO postgres;

--
-- Name: view_municipios; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_municipios AS
 SELECT municipios.id_municipio,
    municipios.id_estado,
    municipios.nombre
   FROM municipios;


ALTER TABLE view_municipios OWNER TO postgres;

--
-- Name: view_parroquias; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_parroquias AS
 SELECT parroquias.id_parroquia,
    parroquias.id_municipio,
    parroquias.nombre
   FROM parroquias;


ALTER TABLE view_parroquias OWNER TO postgres;

--
-- Name: view_personal; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_personal AS
 SELECT personal.id_personal,
    personal.cedula,
    personal.cargo,
    personal.departamento
   FROM personal;


ALTER TABLE view_personal OWNER TO postgres;

--
-- Name: view_usuarios; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW view_usuarios AS
 SELECT usuarios.nombre,
    usuarios.id_personal,
    usuarios."contraseña",
    usuarios.lvl_permisos,
    usuarios.activo
   FROM usuarios;


ALTER TABLE view_usuarios OWNER TO postgres;

--
-- Data for Name: auditorias; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY auditorias (id_auditoria, nombre, descripcion, fecha) FROM stdin;
0	admin	Ingresó al sistema.	2016-11-09 09:33:28.817375
1	admin	Cerró sesion.	2016-11-09 09:42:35.479728
2	admin	Ingresó al sistema.	2016-11-09 09:44:33.946336
3	admin	Se registó al usuario: LFHERNAN con los permisos de usuario nivel 1	2016-11-09 09:49:31.251658
4	admin	Se desactivó la cuenta del usuario: LFHERNAN	2016-11-09 09:53:00.946911
5	admin	Cerró sesion.	2016-11-09 09:53:16.93694
6	admin	Ingresó al sistema.	2016-11-09 09:53:45.92179
7	admin	Cerró sesion.	2016-11-09 09:54:02.48902
8	LFHERNAN	Ingresó al sistema.	2016-11-09 09:54:13.003438
9	LFHERNAN	Cerró sesion.	2016-11-09 09:54:16.763045
10	admin	Ingresó al sistema.	2016-11-09 09:54:23.720657
\.


--
-- Data for Name: cauciones; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY cauciones (id_caucion, id_ficha, dic_imagen, num_caucion) FROM stdin;
\.


--
-- Data for Name: delitos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY delitos (id_delito, desc_delito) FROM stdin;
1	Robo
2	Estafa
3	Hurto simple
4	Hurto vehicular
5	Daños a vehículos
6	Incumplimiento de normas
7	Daños a la propiedad
8	Alteración del orden publico
9	Contra la moral y buenas costumbres
10	Extravios
11	Lesiones
12	Personas perniciosas
13	Usurpación de funciones
14	Otros
\.


--
-- Data for Name: direcciones; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY direcciones (id_direccion, id_parroquia, cedula, lugar) FROM stdin;
\.


--
-- Data for Name: estados; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY estados (nombre, id_estado) FROM stdin;
DTTO. CAPITAL	1
ANZOATEGUI	2
APURE	3
ARAGUA	4
BARINAS	5
BOLIVAR	6
CARABOBO	7
COJEDES	8
FALCON	9
GUARICO	10
LARA	11
MERIDA	12
MIRANDA	13
MONAGAS	14
NUEVA ESPARTA	15
PORTUGUESA	16
SUCRE	17
TACHIRA	18
TRUJILLO	19
YARACUY	20
ZULIA	21
AMAZONAS	22
DELTA AMACURO	23
VARGAS	24
\.


--
-- Data for Name: fichas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY fichas (id_ficha, id_delito, cedula, desc_delito, nro_telefono, edo_civil, profesion, fecha_creacion, fecha_nacimiento, num_ficha) FROM stdin;
\.


--
-- Data for Name: individuos; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY individuos (cedula, p_nombre, p_apellido, s_nombre, s_apellido, sexo, nacionalidad) FROM stdin;
87654321	PrimerN		PrimerA		M	V
123456789	LEIDER	HERNANDEZ	\N	\N	M	v
\.


--
-- Data for Name: modificaciones; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY modificaciones (id_mod, cedula, nuevacedula, pnombre, papellido, snombre, sapellido) FROM stdin;
\.


--
-- Data for Name: municipios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY municipios (id_municipio, id_estado, nombre) FROM stdin;
1	1	LIBERTADOR
2	2	ANACO
3	2	ARAGUA
4	2	BOLIVAR
5	2	BRUZUAL
6	2	CAJIGAL
7	2	FREITES
8	2	INDEPENDENCIA
9	2	LIBERTAD
10	2	MIRANDA
11	2	MONAGAS
12	2	PEÑALVER
13	2	SIMON RODRIGUEZ
14	2	SOTILLO
15	2	GUANIPA
16	2	GUANTA
17	2	PIRITU
18	2	M.L/DIEGO BAUTISTA U
19	2	CARVAJAL
20	2	SANTA ANA
21	2	MC GREGOR
22	2	S JUAN CAPISTRANO
23	3	ACHAGUAS
24	3	MUÑOZ
25	3	PAEZ
26	3	PEDRO CAMEJO
27	3	ROMULO GALLEGOS
28	3	SAN FERNANDO
29	3	BIRUACA
30	4	GIRARDOT
31	4	SANTIAGO MARIÑO
32	4	JOSE FELIX RIVAS
33	4	SAN CASIMIRO
34	4	SAN SEBASTIAN
35	4	SUCRE
36	4	URDANETA
37	4	ZAMORA
38	4	LIBERTADOR
39	4	JOSE ANGEL LAMAS
40	4	BOLIVAR
41	4	SANTOS MICHELENA
42	4	MARIO B IRAGORRY
43	4	TOVAR
44	4	CAMATAGUA
45	4	JOSE R REVENGA
46	4	FRANCISCO LINARES A.
47	4	M.OCUMARE D LA COSTA
48	5	ARISMENDI
49	5	BARINAS
50	5	BOLIVAR
51	5	EZEQUIEL ZAMORA
52	5	OBISPOS
53	5	PEDRAZA
54	5	ROJAS
55	5	SOSA
56	5	ALBERTO ARVELO T
57	5	A JOSE DE SUCRE
58	5	CRUZ PAREDES
59	5	ANDRES E. BLANCO
60	6	CARONI
61	6	CEDEÑO
62	6	HERES
63	6	PIAR
64	6	ROSCIO
65	6	SUCRE
66	6	SIFONTES
67	6	RAUL LEONI
68	6	GRAN SABANA
69	6	EL CALLAO
70	6	PADRE PEDRO CHIEN
71	7	BEJUMA
72	7	CARLOS ARVELO
73	7	DIEGO IBARRA
74	7	GUACARA
75	7	MONTALBAN
76	7	JUAN JOSE MORA
77	7	PUERTO CABELLO
78	7	SAN JOAQUIN
79	7	VALENCIA
80	7	MIRANDA
81	7	LOS GUAYOS
82	7	NAGUANAGUA
83	7	SAN DIEGO
84	7	LIBERTADOR
85	8	ANZOATEGUI
86	8	FALCON
87	8	GIRARDOT
88	8	MP PAO SN J BAUTISTA
89	8	RICAURTE
90	8	SAN CARLOS
91	8	TINACO
92	8	LIMA BLANCO
93	8	ROMULO GALLEGOS
94	9	ACOSTA
95	9	BOLIVAR
96	9	BUCHIVACOA
97	9	CARIRUBANA
98	9	COLINA
99	9	DEMOCRACIA
100	9	FALCON
101	9	FEDERACION
102	9	MAUROA
103	9	MIRANDA
104	9	PETIT
105	9	SILVA
106	9	ZAMORA
107	9	DABAJURO
108	9	MONS. ITURRIZA
109	9	LOS TAQUES
110	9	PIRITU
111	9	UNION
112	9	SAN FRANCISCO
113	9	JACURA
114	9	CACIQUE MANAURE
115	9	PALMA SOLA
116	9	SUCRE
117	9	URUMACO
118	9	TOCOPERO
119	10	INFANTE
120	10	MELLADO
121	10	MIRANDA
122	10	MONAGAS
123	10	RIBAS
124	10	ROSCIO
125	10	ZARAZA
126	10	CAMAGUAN
127	10	S JOSE DE GUARIBE
128	10	LAS MERCEDES
129	10	EL SOCORRO
130	10	ORTIZ
131	10	S MARIA DE IPIRE
132	10	CHAGUARAMAS
133	10	SAN GERONIMO DE G
134	11	CRESPO
135	11	IRIBARREN
136	11	JIMENEZ
137	11	MORAN
138	11	PALAVECINO
139	11	TORRES
140	11	URDANETA
141	11	ANDRES E BLANCO
142	11	SIMON PLANAS
143	12	ALBERTO ADRIANI
144	12	ANDRES BELLO
145	12	ARZOBISPO CHACON
146	12	CAMPO ELIAS
147	12	GUARAQUE
148	12	JULIO CESAR SALAS
149	12	JUSTO BRICEÑO
150	12	LIBERTADOR
151	12	SANTOS MARQUINA
152	12	MIRANDA
153	12	ANTONIO PINTO S.
154	12	OB. RAMOS DE LORA
155	12	CARACCIOLO PARRA
156	12	CARDENAL QUINTERO
157	12	PUEBLO LLANO
158	12	RANGEL
159	12	RIVAS DAVILA
160	12	SUCRE
161	12	TOVAR
162	12	TULIO F CORDERO
163	12	PADRE NOGUERA
164	12	ARICAGUA
165	12	ZEA
166	13	ACEVEDO
167	13	BRION
168	13	GUAICAIPURO
169	13	INDEPENDENCIA
170	13	LANDER
171	13	PAEZ
172	13	PAZ CASTILLO
173	13	PLAZA
174	13	SUCRE
175	13	URDANETA
176	13	ZAMORA
177	13	CRISTOBAL ROJAS
178	13	LOS SALIAS
179	13	ANDRES BELLO
180	13	SIMON BOLIVAR
181	13	BARUTA
182	13	CARRIZAL
183	13	CHACAO
184	13	EL HATILLO
185	13	BUROZ
186	13	PEDRO GUAL
187	14	ACOSTA
188	14	BOLIVAR
189	14	CARIPE
190	14	CEDEÑO
191	14	EZEQUIEL ZAMORA
192	14	LIBERTADOR
193	14	MATURIN
194	14	PIAR
195	14	PUNCERES
196	14	SOTILLO
197	14	AGUASAY
198	14	SANTA BARBARA
199	14	URACOA
200	15	ARISMENDI
201	15	DIAZ
202	15	GOMEZ
203	15	MANEIRO
204	15	MARCANO
205	15	MARIÑO
206	15	PENIN. DE MACANAO
207	15	VILLALBA(I.COCHE)
208	15	TUBORES
209	15	ANTOLIN DEL CAMPO
210	15	GARCIA
211	16	ARAURE
212	16	ESTELLER
213	16	GUANARE
214	16	GUANARITO
215	16	OSPINO
216	16	PAEZ
217	16	SUCRE
218	16	TUREN
219	16	M.JOSE V DE UNDA
220	16	AGUA BLANCA
221	16	PAPELON
222	16	GENARO BOCONOITO
223	16	S RAFAEL DE ONOTO
224	16	SANTA ROSALIA
225	17	ARISMENDI
226	17	BENITEZ
227	17	BERMUDEZ
228	17	CAJIGAL
229	17	MARIÑO
230	17	MEJIA
231	17	MONTES
232	17	RIBERO
233	17	SUCRE
234	17	VALDEZ
235	17	ANDRES E BLANCO
236	17	LIBERTADOR
237	17	ANDRES MATA
238	17	BOLIVAR
239	17	CRUZ S ACOSTA
240	18	AYACUCHO
241	18	BOLIVAR
242	18	INDEPENDENCIA
243	18	CARDENAS
244	18	JAUREGUI
245	18	JUNIN
246	18	LOBATERA
247	18	SAN CRISTOBAL
248	18	URIBANTE
249	18	CORDOBA
250	18	GARCIA DE HEVIA
251	18	GUASIMOS
252	18	MICHELENA
253	18	LIBERTADOR
254	18	PANAMERICANO
255	18	PEDRO MARIA UREÑA
256	18	SUCRE
257	18	ANDRES BELLO
258	18	FERNANDEZ FEO
259	18	LIBERTAD
260	18	SAMUEL MALDONADO
261	18	SEBORUCO
262	18	ANTONIO ROMULO C
263	18	FCO DE MIRANDA
264	18	JOSE MARIA VARGA
265	18	RAFAEL URDANETA
266	18	SIMON RODRIGUEZ
267	18	TORBES
268	18	SAN JUDAS TADEO
269	19	RAFAEL RANGEL
270	19	BOCONO
271	19	CARACHE
272	19	ESCUQUE
273	19	TRUJILLO
274	19	URDANETA
275	19	VALERA
276	19	CANDELARIA
277	19	MIRANDA
278	19	MONTE CARMELO
279	19	MOTATAN
280	19	PAMPAN
281	19	S RAFAEL CARVAJAL
282	19	SUCRE
283	19	ANDRES BELLO
284	19	BOLIVAR
285	19	JOSE F M CAÑIZAL
286	19	JUAN V CAMPO ELI
287	19	LA CEIBA
288	19	PAMPANITO
289	20	BOLIVAR
290	20	BRUZUAL
291	20	NIRGUA
292	20	SAN FELIPE
293	20	SUCRE
294	20	URACHICHE
295	20	PEÑA
296	20	JOSE ANTONIO PAEZ
297	20	LA TRINIDAD
298	20	COCOROTE
299	20	INDEPENDENCIA
300	20	ARISTIDES BASTID
301	20	MANUEL MONGE
302	20	VEROES
303	21	BARALT
304	21	SANTA RITA
305	21	COLON
306	21	MARA
307	21	MARACAIBO
308	21	MIRANDA
309	21	PAEZ
310	21	MACHIQUES DE P
311	21	SUCRE
312	21	LA CAÑADA DE U.
313	21	LAGUNILLAS
314	21	CATATUMBO
315	21	M/ROSARIO DE PERIJA
316	21	CABIMAS
317	21	VALMORE RODRIGUEZ
318	21	JESUS E LOSSADA
319	21	ALMIRANTE P
320	21	SAN FRANCISCO
321	21	JESUS M SEMPRUN
322	21	FRANCISCO J PULG
323	21	SIMON BOLIVAR
324	22	ATURES
325	22	ATABAPO
326	22	MAROA
327	22	RIO NEGRO
328	22	AUTANA
329	22	MANAPIARE
330	22	ALTO ORINOCO
331	23	TUCUPITA
332	23	PEDERNALES
333	23	ANTONIO DIAZ
334	23	CASACOIMA
335	24	VARGAS
\.


--
-- Data for Name: parroquias; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY parroquias (id_parroquia, id_municipio, nombre) FROM stdin;
1	1	ALTAGRACIA
2	1	CANDELARIA
3	1	CATEDRAL
4	1	LA PASTORA
5	1	SAN AGUSTIN
6	1	SAN JOSE
7	1	SAN JUAN
8	1	SANTA ROSALIA
9	1	SANTA TERESA
10	1	SUCRE
11	1	23 DE ENERO
12	1	ANTIMANO
13	1	EL RECREO
14	1	EL VALLE
15	1	LA VEGA
16	1	MACARAO
17	1	CARICUAO
18	1	EL JUNQUITO
19	1	COCHE
20	1	SAN PEDRO
21	1	SAN BERNARDINO
22	1	EL PARAISO
23	2	ANACO
24	2	SAN JOAQUIN
25	3	CM. ARAGUA DE BARCELONA
26	3	CACHIPO
27	4	EL CARMEN
28	4	SAN CRISTOBAL
29	4	BERGANTIN
30	4	CAIGUA
31	4	EL PILAR
32	4	NARICUAL
33	5	CM. CLARINES
34	5	GUANAPE
35	5	SABANA DE UCHIRE
36	6	CM. ONOTO
37	6	SAN PABLO
38	7	CM. CANTAURA
39	7	LIBERTADOR
40	7	SANTA ROSA
41	7	URICA
42	8	CM. SOLEDAD
43	8	MAMO
44	9	CM. SAN MATEO
45	9	EL CARITO
46	9	SANTA INES
47	10	CM. PARIAGUAN
48	10	ATAPIRIRE
49	10	BOCA DEL PAO
50	10	EL PAO
51	11	CM. MAPIRE
52	11	PIAR
53	11	SN DIEGO DE CABRUTICA
54	11	SANTA CLARA
55	11	UVERITO
56	11	ZUATA
57	12	CM. PUERTO PIRITU
58	12	SAN MIGUEL
59	12	SUCRE
60	13	CM. EL TIGRE
61	14	POZUELOS
62	14	CM PTO. LA CRUZ
63	15	CM. SAN JOSE DE GUANIPA
64	16	GUANTA
65	16	CHORRERON
66	17	PIRITU
67	17	SAN FRANCISCO
68	18	LECHERIAS
69	18	EL MORRO
70	19	VALLE GUANAPE
71	19	SANTA BARBARA
72	20	SANTA ANA
73	20	PUEBLO NUEVO
74	21	EL CHAPARRO
75	21	TOMAS ALFARO CALATRAVA
76	22	BOCA UCHIRE
77	22	BOCA DE CHAVEZ
78	23	ACHAGUAS
79	23	APURITO
80	23	EL YAGUAL
81	23	GUACHARA
82	23	MUCURITAS
83	23	QUESERAS DEL MEDIO
84	24	BRUZUAL
85	24	MANTECAL
86	24	QUINTERO
87	24	SAN VICENTE
88	24	RINCON HONDO
89	25	GUASDUALITO
90	25	ARAMENDI
91	25	EL AMPARO
92	25	SAN CAMILO
93	25	URDANETA
94	26	SAN JUAN DE PAYARA
95	26	CODAZZI
96	26	CUNAVICHE
97	27	ELORZA
98	27	LA TRINIDAD
99	28	SAN FERNANDO
100	28	PEÑALVER
101	28	EL RECREO
102	28	SN RAFAEL DE ATAMAICA
103	29	BIRUACA
104	30	CM. LAS DELICIAS
105	30	CHORONI
106	30	MADRE MA DE SAN JOSE
107	30	JOAQUIN CRESPO
108	30	PEDRO JOSE OVALLES
109	30	JOSE CASANOVA GODOY
110	30	ANDRES ELOY BLANCO
111	30	LOS TACARIGUAS
112	31	CM. TURMERO
113	31	SAMAN DE GUERE
114	31	ALFREDO PACHECO M
115	31	CHUAO
116	31	AREVALO APONTE
117	32	CM. LA VICTORIA
118	32	ZUATA
119	32	PAO DE ZARATE
120	32	CASTOR NIEVES RIOS
121	32	LAS GUACAMAYAS
122	33	CM. SAN CASIMIRO
123	33	VALLE MORIN
124	33	GUIRIPA
125	33	OLLAS DE CARAMACATE
126	34	CM. SAN SEBASTIAN
127	35	CM. CAGUA
128	35	BELLA VISTA
129	36	CM. BARBACOAS
130	36	SAN FRANCISCO DE CARA
131	36	TAGUAY
132	36	LAS PEÑITAS
133	37	CM. VILLA DE CURA
134	37	MAGDALENO
135	37	SAN FRANCISCO DE ASIS
136	37	VALLES DE TUCUTUNEMO
137	37	PQ AUGUSTO MIJARES
138	38	CM. PALO NEGRO
139	38	SAN MARTIN DE PORRES
140	39	CM. SANTA CRUZ
141	40	CM. SAN MATEO
142	41	CM. LAS TEJERIAS
143	41	TIARA
144	42	CM. EL LIMON
145	42	CA A DE AZUCAR
146	43	CM. COLONIA TOVAR
147	44	CM. CAMATAGUA
148	44	CARMEN DE CURA
149	45	CM. EL CONSEJO
150	46	CM. SANTA RITA
151	46	FRANCISCO DE MIRANDA
152	46	MONS FELICIANO G
153	47	OCUMARE DE LA COSTA
154	48	ARISMENDI
155	48	GUADARRAMA
156	48	LA UNION
157	48	SAN ANTONIO
158	49	ALFREDO A LARRIVA
159	49	BARINAS
160	49	SAN SILVESTRE
161	49	SANTA INES
162	49	SANTA LUCIA
163	49	TORUNOS
164	49	EL CARMEN
165	49	ROMULO BETANCOURT
166	49	CORAZON DE JESUS
167	49	RAMON I MENDEZ
168	49	ALTO BARINAS
169	49	MANUEL P FAJARDO
170	49	JUAN A RODRIGUEZ D
171	49	DOMINGA ORTIZ P
172	50	ALTAMIRA
173	50	BARINITAS
174	50	CALDERAS
175	51	SANTA BARBARA
176	51	JOSE IGNACIO DEL PUMAR
177	51	RAMON IGNACIO MENDEZ
178	51	PEDRO BRICEÑO MENDEZ
179	52	EL REAL
180	52	LA LUZ
181	52	OBISPOS
182	52	LOS GUASIMITOS
183	53	CIUDAD BOLIVIA
184	53	IGNACIO BRICEÑO
185	53	PAEZ
186	53	JOSE FELIX RIBAS
187	54	DOLORES
188	54	LIBERTAD
189	54	PALACIO FAJARDO
190	54	SANTA ROSA
191	55	CIUDAD DE NUTRIAS
192	55	EL REGALO
193	55	PUERTO DE NUTRIAS
194	55	SANTA CATALINA
195	56	RODRIGUEZ DOMINGUEZ
196	56	SABANETA
197	57	TICOPORO
198	57	NICOLAS PULIDO
199	57	ANDRES BELLO
200	58	BARRANCAS
201	58	EL SOCORRO
202	58	MASPARRITO
203	59	EL CANTON
204	59	SANTA CRUZ DE GUACAS
205	59	PUERTO VIVAS
206	60	SIMON BOLIVAR
207	60	ONCE DE ABRIL
208	60	VISTA AL SOL
209	60	CHIRICA
210	60	DALLA COSTA
211	60	CACHAMAY
212	60	UNIVERSIDAD
213	60	UNARE
214	60	YOCOIMA
215	60	POZO VERDE
216	61	CM. CAICARA DEL ORINOCO
217	61	ASCENSION FARRERAS
218	61	ALTAGRACIA
219	61	LA URBANA
220	61	GUANIAMO
221	61	PIJIGUAOS
222	62	CATEDRAL
223	62	AGUA SALADA
224	62	LA SABANITA
225	62	VISTA HERMOSA
226	62	MARHUANTA
227	62	JOSE ANTONIO PAEZ
228	62	ORINOCO
229	62	PANAPANA
230	62	ZEA
231	63	CM. UPATA
232	63	ANDRES ELOY BLANCO
233	63	PEDRO COVA
234	64	CM. GUASIPATI
235	64	SALOM
236	65	CM. MARIPA
237	65	ARIPAO
238	65	LAS MAJADAS
239	65	MOITACO
240	65	GUARATARO
241	66	CM. TUMEREMO
242	66	DALLA COSTA
243	66	SAN ISIDRO
244	67	CM. CIUDAD PIAR
245	67	SAN FRANCISCO
246	67	BARCELONETA
247	67	SANTA BARBARA
248	68	CM. SANTA ELENA DE UAIREN
249	68	IKABARU
250	69	CM. EL CALLAO
251	70	CM. EL PALMAR
252	71	BEJUMA
253	71	CANOABO
254	71	SIMON BOLIVAR
255	72	GUIGUE
256	72	BELEN
257	72	TACARIGUA
258	73	MARIARA
259	73	AGUAS CALIENTES
260	74	GUACARA
261	74	CIUDAD ALIANZA
262	74	YAGUA
263	75	MONTALBAN
264	76	MORON
265	76	URAMA
266	77	DEMOCRACIA
267	77	FRATERNIDAD
268	77	GOAIGOAZA
269	77	JUAN JOSE FLORES
270	77	BARTOLOME SALOM
271	77	UNION
272	77	BORBURATA
273	77	PATANEMO
274	78	SAN JOAQUIN
275	79	CANDELARIA
276	79	CATEDRAL
277	79	EL SOCORRO
278	79	MIGUEL PEÑA
279	79	SAN BLAS
280	79	SAN JOSE
281	79	SANTA ROSA
282	79	RAFAEL URDANETA
283	79	NEGRO PRIMERO
284	80	MIRANDA
285	81	U LOS GUAYOS
286	82	NAGUANAGUA
287	83	URB SAN DIEGO
288	84	U TOCUYITO
289	84	U INDEPENDENCIA
290	85	COJEDES
291	85	JUAN DE MATA SUAREZ
292	86	TINAQUILLO
293	87	EL BAUL
294	87	SUCRE
295	88	EL PAO
296	89	LIBERTAD DE COJEDES
297	89	EL AMPARO
298	90	SAN CARLOS DE AUSTRIA
299	90	JUAN ANGEL BRAVO
300	90	MANUEL MANRIQUE
301	91	GRL/JEFE JOSE L SILVA
302	92	MACAPO
303	92	LA AGUADITA
304	93	ROMULO GALLEGOS
305	94	SAN JUAN DE LOS CAYOS
306	94	CAPADARE
307	94	LA PASTORA
308	94	LIBERTADOR
309	95	SAN LUIS
310	95	ARACUA
311	95	LA PEÑA
312	96	CAPATARIDA
313	96	BOROJO
314	96	SEQUE
315	96	ZAZARIDA
316	96	BARIRO
317	96	GUAJIRO
318	97	NORTE
319	97	CARIRUBANA
320	97	PUNTA CARDON
321	97	SANTA ANA
322	98	LA VELA DE CORO
323	98	ACURIGUA
324	98	GUAIBACOA
325	98	MACORUCA
326	98	LAS CALDERAS
327	99	PEDREGAL
328	99	AGUA CLARA
329	99	AVARIA
330	99	PIEDRA GRANDE
331	99	PURURECHE
332	100	PUEBLO NUEVO
333	100	ADICORA
334	100	BARAIVED
335	100	BUENA VISTA
336	100	JADACAQUIVA
337	100	MORUY
338	100	EL VINCULO
339	100	EL HATO
340	100	ADAURE
341	101	CHURUGUARA
342	101	AGUA LARGA
343	101	INDEPENDENCIA
344	101	MAPARARI
345	101	EL PAUJI
346	102	MENE DE MAUROA
347	102	CASIGUA
348	102	SAN FELIX
349	103	SAN ANTONIO
350	103	SAN GABRIEL
351	103	SANTA ANA
352	103	GUZMAN GUILLERMO
353	103	MITARE
354	103	SABANETA
355	103	RIO SECO
356	104	CABURE
357	104	CURIMAGUA
358	104	COLINA
359	105	TUCACAS
360	105	BOCA DE AROA
361	106	PUERTO CUMAREBO
362	106	LA CIENAGA
363	106	LA SOLEDAD
364	106	PUEBLO CUMAREBO
365	106	ZAZARIDA
366	107	CM. DABAJURO
367	108	CHICHIRIVICHE
368	108	BOCA DE TOCUYO
369	108	TOCUYO DE LA COSTA
370	109	LOS TAQUES
371	109	JUDIBANA
372	110	PIRITU
373	110	SAN JOSE DE LA COSTA
374	111	STA.CRUZ DE BUCARAL
375	111	EL CHARAL
376	111	LAS VEGAS DEL TUY
377	112	CM. MIRIMIRE
378	113	JACURA
379	113	AGUA LINDA
380	113	ARAURIMA
381	114	CM. YARACAL
382	115	CM. PALMA SOLA
383	116	SUCRE
384	116	PECAYA
385	117	URUMACO
386	117	BRUZUAL
387	118	CM. TOCOPERO
388	119	VALLE DE LA PASCUA
389	119	ESPINO
390	120	EL SOMBRERO
391	120	SOSA
392	121	CALABOZO
393	121	EL CALVARIO
394	121	EL RASTRO
395	121	GUARDATINAJAS
396	122	ALTAGRACIA DE ORITUCO
397	122	LEZAMA
398	122	LIBERTAD DE ORITUCO
399	122	SAN FCO DE MACAIRA
400	122	SAN RAFAEL DE ORITUCO
401	122	SOUBLETTE
402	122	PASO REAL DE MACAIRA
403	123	TUCUPIDO
404	123	SAN RAFAEL DE LAYA
405	124	SAN JUAN DE LOS MORROS
406	124	PARAPARA
407	124	CANTAGALLO
408	125	ZARAZA
409	125	SAN JOSE DE UNARE
410	126	CAMAGUAN
411	126	PUERTO MIRANDA
412	126	UVERITO
413	127	SAN JOSE DE GUARIBE
414	128	LAS MERCEDES
415	128	STA RITA DE MANAPIRE
416	128	CABRUTA
417	129	EL SOCORRO
418	130	ORTIZ
419	130	SAN FCO. DE TIZNADOS
420	130	SAN JOSE DE TIZNADOS
421	130	S LORENZO DE TIZNADOS
422	131	SANTA MARIA DE IPIRE
423	131	ALTAMIRA
424	132	CHAGUARAMAS
425	133	GUAYABAL
426	133	CAZORLA
427	134	FREITEZ
428	134	JOSE MARIA BLANCO
429	135	CATEDRAL
430	135	LA CONCEPCION
431	135	SANTA ROSA
432	135	UNION
433	135	EL CUJI
434	135	TAMACA
435	135	JUAN DE VILLEGAS
436	135	AGUEDO F. ALVARADO
437	135	BUENA VISTA
438	135	JUAREZ
439	136	JUAN B RODRIGUEZ
440	136	DIEGO DE LOZADA
441	136	SAN MIGUEL
442	136	CUARA
443	136	PARAISO DE SAN JOSE
444	136	TINTORERO
445	136	JOSE BERNARDO DORANTE
446	136	CRNEL. MARIANO PERAZA
447	137	BOLIVAR
448	137	ANZOATEGUI
449	137	GUARICO
450	137	HUMOCARO ALTO
451	137	HUMOCARO BAJO
452	137	MORAN
453	137	HILARIO LUNA Y LUNA
454	137	LA CANDELARIA
455	138	CABUDARE
456	138	JOSE G. BASTIDAS
457	138	AGUA VIVA
458	139	TRINIDAD SAMUEL
459	139	ANTONIO DIAZ
460	139	CAMACARO
461	139	CASTAÑEDA
462	139	CHIQUINQUIRA
463	139	ESPINOZA LOS MONTEROS
464	139	LARA
465	139	MANUEL MORILLO
466	139	MONTES DE OCA
467	139	TORRES
468	139	EL BLANCO
469	139	MONTA A VERDE
470	139	HERIBERTO ARROYO
471	139	LAS MERCEDES
472	139	CECILIO ZUBILLAGA
473	139	REYES VARGAS
474	139	ALTAGRACIA
475	140	SIQUISIQUE
476	140	SAN MIGUEL
477	140	XAGUAS
478	140	MOROTURO
479	141	PIO TAMAYO
480	141	YACAMBU
481	141	QBDA. HONDA DE GUACHE
482	142	SARARE
483	142	GUSTAVO VEGAS LEON
484	142	BURIA
485	143	GABRIEL PICON G.
486	143	HECTOR AMABLE MORA
487	143	JOSE NUCETE SARDI
488	143	PULIDO MENDEZ
489	143	PTE. ROMULO GALLEGOS
490	143	PRESIDENTE BETANCOURT
491	143	PRESIDENTE PAEZ
492	144	CM. LA AZULITA
493	145	CM. CANAGUA
494	145	CAPURI
495	145	CHACANTA
496	145	EL MOLINO
497	145	GUAIMARAL
498	145	MUCUTUY
499	145	MUCUCHACHI
500	146	ACEQUIAS
501	146	JAJI
502	146	LA MESA
503	146	SAN JOSE
504	146	MONTALBAN
505	146	MATRIZ
506	146	FERNANDEZ PEÑA
507	147	CM. GUARAQUE
508	147	MESA DE QUINTERO
509	147	RIO NEGRO
510	148	CM. ARAPUEY
511	148	PALMIRA
512	149	CM. TORONDOY
513	149	SAN CRISTOBAL DE T
514	150	ARIAS
515	150	SAGRARIO
516	150	MILLA
517	150	EL LLANO
518	150	JUAN RODRIGUEZ SUAREZ
519	150	JACINTO PLAZA
520	150	DOMINGO PEÑA
521	150	GONZALO PICON FEBRES
522	150	OSUNA RODRIGUEZ
523	150	LASSO DE LA VEGA
524	150	CARACCIOLO PARRA P
525	150	MARIANO PICON SALAS
526	150	ANTONIO SPINETTI DINI
527	150	EL MORRO
528	150	LOS NEVADOS
529	151	CM. TABAY
530	152	CM. TIMOTES
531	152	ANDRES ELOY BLANCO
532	152	PIÑANGO
533	152	LA VENTA
534	153	CM. STA CRUZ DE MORA
535	153	MESA BOLIVAR
536	153	MESA DE LAS PALMAS
537	154	CM. STA ELENA DE ARENALES
538	154	ELOY PAREDES
539	154	PQ R DE ALCAZAR
540	155	CM. TUCANI
541	155	FLORENCIO RAMIREZ
542	156	CM. SANTO DOMINGO
543	156	LAS PIEDRAS
544	157	CM. PUEBLO LLANO
545	158	CM. MUCUCHIES
546	158	MUCURUBA
547	158	SAN RAFAEL
548	158	CACUTE
549	158	LA TOMA
550	159	CM. BAILADORES
551	159	GERONIMO MALDONADO
552	160	CM. LAGUNILLAS
553	160	CHIGUARA
554	160	ESTANQUES
555	160	SAN JUAN
556	160	PUEBLO NUEVO DEL SUR
557	160	LA TRAMPA
558	161	EL LLANO
559	161	TOVAR
560	161	EL AMPARO
561	161	SAN FRANCISCO
562	162	CM. NUEVA BOLIVIA
563	162	INDEPENDENCIA
564	162	MARIA C PALACIOS
565	162	SANTA APOLONIA
566	163	CM. STA MARIA DE CAPARO
567	164	CM. ARICAGUA
568	164	SAN ANTONIO
569	165	CM. ZEA
570	165	CAÑO EL TIGRE
571	166	CAUCAGUA
572	166	ARAGUITA
573	166	AREVALO GONZALEZ
574	166	CAPAYA
575	166	PANAQUIRE
576	166	RIBAS
577	166	EL CAFE
578	166	MARIZAPA
579	167	HIGUEROTE
580	167	CURIEPE
581	167	TACARIGUA
582	168	LOS TEQUES
583	168	CECILIO ACOSTA
584	168	PARACOTOS
585	168	SAN PEDRO
586	168	TACATA
587	168	EL JARILLO
588	168	ALTAGRACIA DE LA M
589	169	STA TERESA DEL TUY
590	169	EL CARTANAL
591	170	OCUMARE DEL TUY
592	170	LA DEMOCRACIA
593	170	SANTA BARBARA
594	171	RIO CHICO
595	171	EL GUAPO
596	171	TACARIGUA DE LA LAGUNA
597	171	PAPARO
598	171	SN FERNANDO DEL GUAPO
599	172	SANTA LUCIA
600	173	GUARENAS
601	174	PETARE
602	174	LEONCIO MARTINEZ
603	174	CAUCAGUITA
604	174	FILAS DE MARICHES
605	174	LA DOLORITA
606	175	CUA
607	175	NUEVA CUA
608	176	GUATIRE
609	176	BOLIVAR
610	177	CHARALLAVE
611	177	LAS BRISAS
612	178	SAN ANTONIO LOS ALTOS
613	179	SAN JOSE DE BARLOVENTO
614	179	CUMBO
615	180	SAN FCO DE YARE
616	180	S ANTONIO DE YARE
617	181	BARUTA
618	181	EL CAFETAL
619	181	LAS MINAS DE BARUTA
620	182	CARRIZAL
621	183	CHACAO
622	184	EL HATILLO
623	185	MAMPORAL
624	186	CUPIRA
625	186	MACHURUCUTO
626	187	CM. SAN ANTONIO
627	187	SAN FRANCISCO
628	188	CM. CARIPITO
629	189	CM. CARIPE
630	189	TERESEN
631	189	EL GUACHARO
632	189	SAN AGUSTIN
633	189	LA GUANOTA
634	189	SABANA DE PIEDRA
635	190	CM. CAICARA
636	190	AREO
637	190	SAN FELIX
638	190	VIENTO FRESCO
639	191	CM. PUNTA DE MATA
640	191	EL TEJERO
641	192	CM. TEMBLADOR
642	192	TABASCA
643	192	LAS ALHUACAS
644	192	CHAGUARAMAS
645	193	EL FURRIAL
646	193	JUSEPIN
647	193	EL COROZO
648	193	SAN VICENTE
649	193	LA PICA
650	193	ALTO DE LOS GODOS
651	193	BOQUERON
652	193	LAS COCUIZAS
653	193	SANTA CRUZ
654	193	SAN SIMON
655	194	CM. ARAGUA
656	194	CHAGUARAMAL
657	194	GUANAGUANA
658	194	APARICIO
659	194	TAGUAYA
660	194	EL PINTO
661	194	LA TOSCANA
662	195	CM. QUIRIQUIRE
663	195	CACHIPO
664	196	CM. BARRANCAS
665	196	LOS BARRANCOS DE FAJARDO
666	197	CM. AGUASAY
667	198	CM. SANTA BARBARA
668	199	CM. URACOA
669	200	CM. LA ASUNCION
670	201	CM. SAN JUAN BAUTISTA
671	201	ZABALA
672	202	CM. SANTA ANA
673	202	GUEVARA
674	202	MATASIETE
675	202	BOLIVAR
676	202	SUCRE
677	203	CM. PAMPATAR
678	203	AGUIRRE
679	204	CM. JUAN GRIEGO
680	204	ADRIAN
681	205	CM. PORLAMAR
682	206	CM. BOCA DEL RIO
683	206	SAN FRANCISCO
684	207	CM. SAN PEDRO DE COCHE
685	207	VICENTE FUENTES
686	208	CM. PUNTA DE PIEDRAS
687	208	LOS BARALES
688	209	CM.LA PLAZA DE PARAGUACHI
689	210	CM. VALLE ESP SANTO
690	210	FRANCISCO FAJARDO
691	211	CM. ARAURE
692	211	RIO ACARIGUA
693	212	CM. PIRITU
694	212	UVERAL
695	213	CM. GUANARE
696	213	CORDOBA
697	213	SAN JUAN GUANAGUANARE
698	213	VIRGEN DE LA COROMOTO
699	213	SAN JOSE DE LA MONTAÑA
700	214	CM. GUANARITO
701	214	TRINIDAD DE LA CAPILLA
702	214	DIVINA PASTORA
703	215	CM. OSPINO
704	215	APARICION
705	215	LA ESTACION
706	216	CM. ACARIGUA
707	216	PAYARA
708	216	PIMPINELA
709	216	RAMON PERAZA
710	217	CM. BISCUCUY
711	217	CONCEPCION
712	217	SAN RAFAEL PALO ALZADO
713	217	UVENCIO A VELASQUEZ
714	217	SAN JOSE DE SAGUAZ
715	217	VILLA ROSA
716	218	CM. VILLA BRUZUAL
717	218	CANELONES
718	218	SANTA CRUZ
719	218	SAN ISIDRO LABRADOR
720	219	CM. CHABASQUEN
721	219	PEÑA BLANCA
722	220	CM. AGUA BLANCA
723	221	CM. PAPELON
724	221	CAÑO DELGADITO
725	222	CM. BOCONOITO
726	222	ANTOLIN TOVAR AQUINO
727	223	CM. SAN RAFAEL DE ONOTO
728	223	SANTA FE
729	223	THERMO MORLES
730	224	CM. EL PLAYON
731	224	FLORIDA
732	225	RIO CARIBE
733	225	SAN JUAN GALDONAS
734	225	PUERTO SANTO
735	225	EL MORRO DE PTO SANTO
736	225	ANTONIO JOSE DE SUCRE
737	226	EL PILAR
738	226	EL RINCON
739	226	GUARAUNOS
740	226	TUNAPUICITO
741	226	UNION
742	226	GRAL FCO. A VASQUEZ
743	227	SANTA CATALINA
744	227	SANTA ROSA
745	227	SANTA TERESA
746	227	BOLIVAR
747	227	MACARAPANA
748	228	YAGUARAPARO
749	228	LIBERTAD
750	228	PAUJIL
751	229	IRAPA
752	229	CAMPO CLARO
753	229	SORO
754	229	SAN ANTONIO DE IRAPA
755	229	MARABAL
756	230	CM. SAN ANT DEL GOLFO
757	231	CUMANACOA
758	231	ARENAS
759	231	ARICAGUA
760	231	COCOLLAR
761	231	SAN FERNANDO
762	231	SAN LORENZO
763	232	CARIACO
764	232	CATUARO
765	232	RENDON
766	232	SANTA CRUZ
767	232	SANTA MARIA
768	233	ALTAGRACIA
769	233	AYACUCHO
770	233	SANTA INES
771	233	VALENTIN VALIENTE
772	233	SAN JUAN
773	233	GRAN MARISCAL
774	233	RAUL LEONI
775	234	GUIRIA
776	234	CRISTOBAL COLON
777	234	PUNTA DE PIEDRA
778	234	BIDEAU
779	235	MARIÑO
780	235	ROMULO GALLEGOS
781	236	TUNAPUY
782	236	CAMPO ELIAS
783	237	SAN JOSE DE AREOCUAR
784	237	TAVERA ACOSTA
785	238	CM. MARIGUITAR
786	239	ARAYA
787	239	MANICUARE
788	239	CHACOPATA
789	240	CM. COLON
790	240	RIVAS BERTI
791	240	SAN PEDRO DEL RIO
792	241	CM. SAN ANT DEL TACHIRA
793	241	PALOTAL
794	241	JUAN VICENTE GOMEZ
795	241	ISAIAS MEDINA ANGARIT
796	242	CM. CAPACHO NUEVO
797	242	JUAN GERMAN ROSCIO
798	242	ROMAN CARDENAS
799	243	CM. TARIBA
800	243	LA FLORIDA
801	243	AMENODORO RANGEL LAMU
802	244	CM. LA GRITA
803	244	EMILIO C. GUERRERO
804	244	MONS. MIGUEL A SALAS
805	245	CM. RUBIO
806	245	BRAMON
807	245	LA PETROLEA
808	245	QUINIMARI
809	246	CM. LOBATERA
810	246	CONSTITUCION
811	247	LA CONCORDIA
812	247	PEDRO MARIA MORANTES
813	247	SN JUAN BAUTISTA
814	247	SAN SEBASTIAN
815	247	DR. FCO. ROMERO LOBO
816	248	CM. PREGONERO
817	248	CARDENAS
818	248	POTOSI
819	248	JUAN PABLO PEÑALOZA
820	249	CM. STA. ANA  DEL TACHIRA
821	250	CM. LA FRIA
822	250	BOCA DE GRITA
823	250	JOSE ANTONIO PAEZ
824	251	CM. PALMIRA
825	252	CM. MICHELENA
826	253	CM. ABEJALES
827	253	SAN JOAQUIN DE NAVAY
828	253	DORADAS
829	253	EMETERIO OCHOA
830	254	CM. COLONCITO
831	254	LA PALMITA
832	255	CM. UREÑA
833	255	NUEVA ARCADIA
834	256	CM. QUENIQUEA
835	256	SAN PABLO
836	256	ELEAZAR LOPEZ CONTRERA
837	257	CM. CORDERO
838	258	CM.SAN RAFAEL DEL PINAL
839	258	SANTO DOMINGO
840	258	ALBERTO ADRIANI
841	259	CM. CAPACHO VIEJO
842	259	CIPRIANO CASTRO
843	259	MANUEL FELIPE RUGELES
844	260	CM. LA TENDIDA
845	260	BOCONO
846	260	HERNANDEZ
847	261	CM. SEBORUCO
848	262	CM. LAS MESAS
849	263	CM. SAN JOSE DE BOLIVAR
850	264	CM. EL COBRE
851	265	CM. DELICIAS
852	266	CM. SAN SIMON
853	267	CM. SAN JOSECITO
854	268	CM. UMUQUENA
855	269	BETIJOQUE
856	269	JOSE G HERNANDEZ
857	269	LA PUEBLITA
858	269	EL CEDRO
859	270	BOCONO
860	270	EL CARMEN
861	270	MOSQUEY
862	270	AYACUCHO
863	270	BURBUSAY
864	270	GENERAL RIVAS
865	270	MONSEÑOR JAUREGUI
866	270	RAFAEL RANGEL
867	270	SAN JOSE
868	270	SAN MIGUEL
869	270	GUARAMACAL
870	270	LA VEGA DE GUARAMACAL
871	271	CARACHE
872	271	LA CONCEPCION
873	271	CUICAS
874	271	PANAMERICANA
875	271	SANTA CRUZ
876	272	ESCUQUE
877	272	SABANA LIBRE
878	272	LA UNION
879	272	SANTA RITA
880	273	CRISTOBAL MENDOZA
881	273	CHIQUINQUIRA
882	273	MATRIZ
883	273	MONSEÑOR CARRILLO
884	273	CRUZ CARRILLO
885	273	ANDRES LINARES
886	273	TRES ESQUINAS
887	274	LA QUEBRADA
888	274	JAJO
889	274	LA MESA
890	274	SANTIAGO
891	274	CABIMBU
892	274	TUÑAME
893	275	MERCEDES DIAZ
894	275	JUAN IGNACIO MONTILLA
895	275	LA BEATRIZ
896	275	MENDOZA
897	275	LA PUERTA
898	275	SAN LUIS
899	276	CHEJENDE
900	276	CARRILLO
901	276	CEGARRA
902	276	BOLIVIA
903	276	MANUEL SALVADOR ULLOA
904	276	SAN JOSE
905	276	ARNOLDO GABALDON
906	277	EL DIVIDIVE
907	277	AGUA CALIENTE
908	277	EL CENIZO
909	277	AGUA SANTA
910	277	VALERITA
911	278	MONTE CARMELO
912	278	BUENA VISTA
913	278	STA MARIA DEL HORCON
914	279	MOTATAN
915	279	EL BAÑO
916	279	JALISCO
917	280	PAMPAN
918	280	SANTA ANA
919	280	LA PAZ
920	280	FLOR DE PATRIA
921	281	CARVAJAL
922	281	ANTONIO N BRICEÑO
923	281	CAMPO ALEGRE
924	281	JOSE LEONARDO SUAREZ
925	282	SABANA DE MENDOZA
926	282	JUNIN
927	282	VALMORE RODRIGUEZ
928	282	EL PARAISO
929	283	SANTA ISABEL
930	283	ARAGUANEY
931	283	EL JAGUITO
932	283	LA ESPERANZA
933	284	SABANA GRANDE
934	284	CHEREGUE
935	284	GRANADOS
936	285	EL SOCORRO
937	285	LOS CAPRICHOS
938	285	ANTONIO JOSE DE SUCRE
939	286	CAMPO ELIAS
940	286	ARNOLDO GABALDON
941	287	SANTA APOLONIA
942	287	LA CEIBA
943	287	EL PROGRESO
944	287	TRES DE FEBRERO
945	288	PAMPANITO
946	288	PAMPANITO II
947	288	LA CONCEPCION
948	289	CM. AROA
949	290	CM. CHIVACOA
950	290	CAMPO ELIAS
951	291	CM. NIRGUA
952	291	SALOM
953	291	TEMERLA
954	292	CM. SAN FELIPE
955	292	ALBARICO
956	292	SAN JAVIER
957	293	CM. GUAMA
958	294	CM. URACHICHE
959	295	CM. YARITAGUA
960	295	SAN ANDRES
961	296	CM. SABANA DE PARRA
962	297	CM. BORAURE
963	298	CM. COCOROTE
964	299	CM. INDEPENDENCIA
965	300	CM. SAN PABLO
966	301	CM. YUMARE
967	302	CM. FARRIAR
968	302	EL GUAYABO
969	303	GENERAL URDANETA
970	303	LIBERTADOR
971	303	MANUEL GUANIPA MATOS
972	303	MARCELINO BRICEÑO
973	303	SAN TIMOTEO
974	303	PUEBLO NUEVO
975	304	PEDRO LUCAS URRIBARRI
976	304	SANTA RITA
977	304	JOSE CENOVIO URRIBARR
978	304	EL MENE
979	305	SANTA CRUZ DEL ZULIA
980	305	URRIBARRI
981	305	MORALITO
982	305	SAN CARLOS DEL ZULIA
983	305	SANTA BARBARA
984	306	LUIS DE VICENTE
985	306	RICAURTE
986	306	MONS.MARCOS SERGIO G
987	306	SAN RAFAEL
988	306	LAS PARCELAS
989	306	TAMARE
990	306	LA SIERRITA
991	307	BOLIVAR
992	307	COQUIVACOA
993	307	CRISTO DE ARANZA
994	307	CHIQUINQUIRA
995	307	SANTA LUCIA
996	307	OLEGARIO VILLALOBOS
997	307	JUANA DE AVILA
998	307	CARACCIOLO PARRA PEREZ
999	307	IDELFONZO VASQUEZ
1000	307	CACIQUE MARA
1001	307	CECILIO ACOSTA
1002	307	RAUL LEONI
1003	307	FRANCISCO EUGENIO B
1004	307	MANUEL DAGNINO
1005	307	LUIS HURTADO HIGUERA
1006	307	VENANCIO PULGAR
1007	307	ANTONIO BORJAS ROMERO
1008	307	SAN ISIDRO
1009	308	FARIA
1010	308	SAN ANTONIO
1011	308	ANA MARIA CAMPOS
1012	308	SAN JOSE
1013	308	ALTAGRACIA
1014	309	GOAJIRA
1015	309	ELIAS SANCHEZ RUBIO
1016	309	SINAMAICA
1017	309	ALTA GUAJIRA
1018	310	SAN JOSE DE PERIJA
1019	310	BARTOLOME DE LAS CASAS
1020	310	LIBERTAD
1021	310	RIO NEGRO
1022	311	GIBRALTAR
1023	311	HERAS
1024	311	M.ARTURO CELESTINO A
1025	311	ROMULO GALLEGOS
1026	311	BOBURES
1027	311	EL BATEY
1028	312	ANDRES BELLO (KM 48)
1029	312	POTRERITOS
1030	312	EL CARMELO
1031	312	CHIQUINQUIRA
1032	312	CONCEPCION
1033	313	ELEAZAR LOPEZ C
1034	313	ALONSO DE OJEDA
1035	313	VENEZUELA
1036	313	CAMPO LARA
1037	313	LIBERTAD
1038	314	UDON PEREZ
1039	314	ENCONTRADOS
1040	315	DONALDO GARCIA
1041	315	SIXTO ZAMBRANO
1042	315	EL ROSARIO
1043	316	AMBROSIO
1044	316	GERMAN RIOS LINARES
1045	316	JORGE HERNANDEZ
1046	316	LA ROSA
1047	316	PUNTA GORDA
1048	316	CARMEN HERRERA
1049	316	SAN BENITO
1050	316	ROMULO BETANCOURT
1051	316	ARISTIDES CALVANI
1052	317	RAUL CUENCA
1053	317	LA VICTORIA
1054	317	RAFAEL URDANETA
1055	318	JOSE RAMON YEPEZ
1056	318	LA CONCEPCION
1057	318	SAN JOSE
1058	318	MARIANO PARRA LEON
1059	319	MONAGAS
1060	319	ISLA DE TOAS
1061	320	MARCIAL HERNANDEZ
1062	320	FRANCISCO OCHOA
1063	320	SAN FRANCISCO
1064	320	EL BAJO
1065	320	DOMITILA FLORES
1066	320	LOS CORTIJOS
1067	321	BARI
1068	321	JESUS M SEMPRUN
1069	322	SIMON RODRIGUEZ
1070	322	CARLOS QUEVEDO
1071	322	FRANCISCO J PULGAR
1072	323	RAFAEL MARIA BARALT
1073	323	MANUEL MANRIQUE
1074	323	RAFAEL URDANETA
1075	324	FERNANDO GIRON TOVAR
1076	324	LUIS ALBERTO GOMEZ
1077	324	PARHUEÑA
1078	324	PLATANILLAL
1079	325	CM. SAN FERNANDO DE ATABA
1080	325	UCATA
1081	325	YAPACANA
1082	325	CANAME
1083	326	CM. MAROA
1084	326	VICTORINO
1085	326	COMUNIDAD
1086	327	CM. SAN CARLOS DE RIO NEG
1087	327	SOLANO
1088	327	COCUY
1089	328	CM. ISLA DE RATON
1090	328	SAMARIAPO
1091	328	SIPAPO
1092	328	MUNDUAPO
1093	328	GUAYAPO
1094	329	CM. SAN JUAN DE MANAPIARE
1095	329	ALTO VENTUARI
1096	329	MEDIO VENTUARI
1097	329	BAJO VENTUARI
1098	330	CM. LA ESMERALDA
1099	330	HUACHAMACARE
1100	330	MARAWAKA
1101	330	MAVACA
1102	330	SIERRA PARIMA
1103	331	SAN JOSE
1104	331	VIRGEN DEL VALLE
1105	331	SAN RAFAEL
1106	331	JOSE VIDAL MARCANO
1107	331	LEONARDO RUIZ PINEDA
1108	331	MONS. ARGIMIRO GARCIA
1109	331	MCL.ANTONIO J DE SUCRE
1110	331	JUAN MILLAN
1111	332	PEDERNALES
1112	332	LUIS B PRIETO FIGUERO
1113	333	CURIAPO
1114	333	SANTOS DE ABELGAS
1115	333	MANUEL RENAUD
1116	333	PADRE BARRAL
1117	333	ANICETO LUGO
1118	333	ALMIRANTE LUIS BRION
1119	334	IMATACA
1120	334	ROMULO GALLEGOS
1121	334	JUAN BAUTISTA ARISMEN
1122	334	MANUEL PIAR
1123	334	5 DE JULIO
1124	335	CARABALLEDA
1125	335	CARAYACA
1126	335	CARUAO
1127	335	CATIA LA MAR
1128	335	LA GUAIRA
1129	335	MACUTO
1130	335	MAIQUETIA
1131	335	NAIGUATA
1132	335	EL JUNKO
1133	335	PQ RAUL LEONI
1134	335	PQ CARLOS SOUBLETTE
\.


--
-- Data for Name: personal; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY personal (id_personal, cedula, cargo, departamento) FROM stdin;
0	87654321	Soporte	Dpto. Informatica soporte
1	123456789	cargo	departamento
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY usuarios (nombre, id_personal, "contraseña", lvl_permisos, activo) FROM stdin;
admin	0	12345	3	1
LFHERNAN	1	12345678	1	1
\.


--
-- Name: auditorias_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY auditorias
    ADD CONSTRAINT auditorias_pkey PRIMARY KEY (id_auditoria);


--
-- Name: cauciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY cauciones
    ADD CONSTRAINT cauciones_pkey PRIMARY KEY (id_caucion);


--
-- Name: delitos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delitos
    ADD CONSTRAINT delitos_pkey PRIMARY KEY (id_delito);


--
-- Name: direcciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY direcciones
    ADD CONSTRAINT direcciones_pkey PRIMARY KEY (id_direccion);


--
-- Name: estados_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY estados
    ADD CONSTRAINT estados_pkey PRIMARY KEY (id_estado);


--
-- Name: fichas_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY fichas
    ADD CONSTRAINT fichas_pkey PRIMARY KEY (id_ficha);


--
-- Name: individuos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY individuos
    ADD CONSTRAINT individuos_pkey PRIMARY KEY (cedula);


--
-- Name: modificaciones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY modificaciones
    ADD CONSTRAINT modificaciones_pkey PRIMARY KEY (id_mod);


--
-- Name: municipios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY municipios
    ADD CONSTRAINT municipios_pkey PRIMARY KEY (id_municipio);


--
-- Name: parroquias_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY parroquias
    ADD CONSTRAINT parroquias_pkey PRIMARY KEY (id_parroquia);


--
-- Name: personal_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY personal
    ADD CONSTRAINT personal_pkey PRIMARY KEY (id_personal);


--
-- Name: usuarios_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (nombre);


--
-- Name: index_cauciones; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX index_cauciones ON cauciones USING btree (id_caucion);


--
-- Name: index_fichas; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX index_fichas ON fichas USING btree (id_ficha);


--
-- Name: index_individuos; Type: INDEX; Schema: public; Owner: postgres
--

CREATE UNIQUE INDEX index_individuos ON individuos USING btree (cedula);


--
-- Name: auditorias_nombre_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY auditorias
    ADD CONSTRAINT auditorias_nombre_fkey FOREIGN KEY (nombre) REFERENCES usuarios(nombre);


--
-- Name: cauciones_id_ficha_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY cauciones
    ADD CONSTRAINT cauciones_id_ficha_fkey FOREIGN KEY (id_ficha) REFERENCES fichas(id_ficha);


--
-- Name: direcciones_cedula_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY direcciones
    ADD CONSTRAINT direcciones_cedula_fkey FOREIGN KEY (cedula) REFERENCES individuos(cedula);


--
-- Name: direcciones_id_parroquia_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY direcciones
    ADD CONSTRAINT direcciones_id_parroquia_fkey FOREIGN KEY (id_parroquia) REFERENCES parroquias(id_parroquia);


--
-- Name: fichas_cedula_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY fichas
    ADD CONSTRAINT fichas_cedula_fkey FOREIGN KEY (cedula) REFERENCES individuos(cedula);


--
-- Name: fichas_id_delito_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY fichas
    ADD CONSTRAINT fichas_id_delito_fkey FOREIGN KEY (id_delito) REFERENCES delitos(id_delito);


--
-- Name: modificaciones_cedula_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY modificaciones
    ADD CONSTRAINT modificaciones_cedula_fkey FOREIGN KEY (cedula) REFERENCES individuos(cedula);


--
-- Name: municipios_id_estado_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY municipios
    ADD CONSTRAINT municipios_id_estado_fkey FOREIGN KEY (id_estado) REFERENCES estados(id_estado);


--
-- Name: parroquias_id_municipio_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY parroquias
    ADD CONSTRAINT parroquias_id_municipio_fkey FOREIGN KEY (id_municipio) REFERENCES municipios(id_municipio);


--
-- Name: personal_cedula_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY personal
    ADD CONSTRAINT personal_cedula_fkey FOREIGN KEY (cedula) REFERENCES individuos(cedula);


--
-- Name: usuarios_id_personal_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY usuarios
    ADD CONSTRAINT usuarios_id_personal_fkey FOREIGN KEY (id_personal) REFERENCES personal(id_personal);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

