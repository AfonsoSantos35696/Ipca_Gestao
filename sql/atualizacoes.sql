-- Atualiza todas as passwords em texto simples para a hash da password '123456'
-- Esta é uma medida provisória para garantir que utilizadores antigos continuam a conseguir entrar após a transição de segurança.
-- A recomendação é que forcem todos os utilizadores a redefinirem as passwords logo de seguida!

/*
  Hash correspondente a '123456' em php usando PASSWORD_DEFAULT:
  $2y$10$Fw4gQ6B2G.c6.oI9I3.sZe8yXz0l0eQWExBq1W1tZzT8.4gQp7bO6
*/

UPDATE utilizadores 
SET password = '$2y$10$Fw4gQ6B2G.c6.oI9I3.sZe8yXz0l0eQWExBq1W1tZzT8.4gQp7bO6'
WHERE LENGTH(password) < 60;
