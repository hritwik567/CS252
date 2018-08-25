/* credit @Daniel Scocco */

/****************** SERVER CODE ****************/
#include <stdio.h>
#include <stdlib.h>
#include <netinet/in.h>
#include <string.h>
#include <sys/socket.h>
#include <arpa/inet.h>

void toLower(char a[],int n){
  int i=0;
  for(i=0;i<n;i++){
    a[i]=(a[i]>='A'&&a[i]<='Z')?(a[i]-'A'+'a'):a[i];
  }
}

int MAX_SIZE = 1024;

int main(){
  int welcomeSocket, newSocket;
  char buffer[1024];
  struct sockaddr_in serverAddr;
  struct sockaddr_storage serverStorage;
  socklen_t addr_size;
  int num;
  /*---- Create the socket. The three arguments are: ----*/
  /* 1) Internet domain 2) Stream socket 3) Default protocol (TCP in this case) */
  welcomeSocket = socket(PF_INET, SOCK_STREAM, 0);

  /*---- Configure settings of the server address struct ----*/
  /* Address family = Internet */
  serverAddr.sin_family = AF_INET;
  /* Set port number, using htons function to use proper byte order */
  serverAddr.sin_port = htons(5433);
  /* Set IP address to localhost */
  serverAddr.sin_addr.s_addr = inet_addr("127.0.0.1");
  /* Set all bits of the padding field to 0 */
  memset(serverAddr.sin_zero, '\0', sizeof serverAddr.sin_zero);

  /*---- Bind the address struct to the socket ----*/
  bind(welcomeSocket, (struct sockaddr *) &serverAddr, sizeof(serverAddr));

  /*---- Listen on the socket, with 5 max connection requests queued ----*/
  if(listen(welcomeSocket,5)==0)
    printf("I'm listening\n");
  else
    printf("Error\n");

  /*---- Accept call creates a new socket for the incoming connection ----*/
  addr_size = sizeof serverStorage;
  strcpy(buffer,"Hello World\n");
  int pid;
  while(1){
    newSocket = accept(welcomeSocket, (struct sockaddr *) &serverStorage, &addr_size);
  
    /*---- Send message to the socket of the incoming connection ----*/
    
    // while(1){
    //   num = recv(newSocket,buffer,MAX_SIZE,0);
    //   if(num<=0){
    //     printf("Nothing received\n");
    //     break;
    //   }
    //   buffer[num] = '\0';
    //   toLower(buffer,num);
    //   send(newSocket,buffer,strlen(buffer),0);
    // }
    
    pid=fork();
    if(pid==0)//child process rec and send
    {
     //rceive from client
     while(1)
     {
      num=recv(newSocket,buffer,MAX_SIZE,0);
      if(num==0)
      {
       close(newSocket);
       break;
      }
      buffer[num] = '\0';
      send(newSocket,buffer,num,0);

      printf("Receive and set:%s\n",buffer);
     }//close interior while
    exit(0);
    }
    else
    {
     close(newSocket);//sock is closed BY PARENT
    }

    send(newSocket,buffer,13,0);
  }
  return 0;
}
