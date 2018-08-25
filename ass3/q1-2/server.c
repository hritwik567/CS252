/* credit @Daniel Scocco */

/****************** SERVER CODE ****************/

#include <stdio.h>
#include <netinet/in.h>
#include <string.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#define MAXSIZE 1024

void toLower(char a[],int n){
  for(int i=0;i<n;i++){
    a[i]=(a[i]>='A'&&a[i]<='Z')?(a[i]-'A'+'a'):a[i];
  }
}

int main(){
  int welcomeSocket, newSocket, num;
  char buffer[MAXSIZE];
  struct sockaddr_in serverAddr;
  struct sockaddr_storage serverStorage;
  socklen_t addr_size;

  /*---- Create the socket. The three arguments are: ----*/
  /* 1) Internet domain 2) Stream socket 3) Default protocol (TCP in this case) */
  welcomeSocket = socket(PF_INET, SOCK_STREAM, 0);

  /*---- Configure settings of the server address struct ----*/
  /* Address family = Internet */
  serverAddr.sin_family = AF_INET;
  /* Set port number, using htons function to use proper byte order */
  serverAddr.sin_port = htons(5432);
  /* Set IP address to localhost */
  serverAddr.sin_addr.s_addr = inet_addr("127.0.0.1");
  /* Set all bits of the padding field to 0 */
  memset(serverAddr.sin_zero, '\0', sizeof serverAddr.sin_zero);

  /*---- Bind the address struct to the socket ----*/
  bind(welcomeSocket, (struct sockaddr *) &serverAddr, sizeof(serverAddr));

  /*---- Listen on the socket, with 5 max connection requests queued ----*/
  if(listen(welcomeSocket,5)==0){
    printf("I'm listening\n");
  } else {
    printf("Connection Error\n");
  }

  while(1){
    addr_size = sizeof serverStorage;
    newSocket = accept(welcomeSocket, (struct sockaddr *) &serverStorage, &addr_size);
    printf("Server got connection from a client\n");

    while(1){
      /*---- Accept call creates a new socket for the incoming connection ----*/
      num = recv(newSocket, buffer, MAXSIZE,0);
      if(num==0){
        printf("Connection Closed\n");
        break;
      }
      buffer[num] = '\0';
      printf("Server:Msg Received %s\n", buffer);
      /*---- Send message to the socket of the incoming connection ----*/
      toLower(buffer, strlen(buffer));
      if ((send(newSocket, buffer, strlen(buffer),0))== -1){
           fprintf(stderr, "Failure Sending Message\n");
           close(newSocket);
           break;
      }
      printf("Server:Msg being sent: %s\nNumber of bytes sent: %lu\n", buffer, strlen(buffer));
    }
    close(newSocket);
  }
  close(welcomeSocket);
  return 0;
}
